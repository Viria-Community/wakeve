<?php

namespace App\Services;

use App\Enums\CompanyStatus;
use App\Exceptions\InvalidCompanyStatusTransitionException;
use App\Models\Company;
use App\Models\CompanyStatusHistory;
use App\Models\DocumentType;
use App\Models\User;
use App\Notifications\CompanyApplicationResubmittedNotification;
use App\Notifications\CompanyStatusChangedNotification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;

/**
 * Point d'écriture UNIQUE pour le statut d'une compagnie (ADR-005).
 * Aucun contrôleur, job, ou modèle ne doit modifier `companies.status`
 * autrement qu'en passant par ce service.
 */
class CompanyStatusService
{
    /**
     * Valide et active une compagnie : pending -> validated -> active.
     * Les deux transitions sont journalisées séparément dans la même
     * transaction (ADR-005) ; la seconde est attribuée au système
     * (decided_by = NULL).
     */
    public function validateAndActivate(Company $company, User $decidedBy): void
    {
        DB::transaction(function () use ($company, $decidedBy) {
            $locked = $this->lockAndAssertStatus($company, CompanyStatus::Pending);
            $this->assertDocumentsAllowValidation($locked);

            $this->recordHistory($locked, CompanyStatus::Pending, CompanyStatus::Validated, 'Validation administrative du dossier', $decidedBy);
            $this->recordHistory($locked, CompanyStatus::Validated, CompanyStatus::Active, 'Activation automatique après validation', null);

            $locked->status = CompanyStatus::Active->value;
            $locked->validated_at = now();
            $locked->save();
            $this->notifyManagers($locked, CompanyStatus::Active, null, route('dashboard', [], false));

            $company->setRawAttributes($locked->getAttributes(), true);
        });
    }

    /**
     * Rejette le dossier d'une compagnie : pending -> rejected.
     */
    public function reject(Company $company, User $decidedBy, string $reason): void
    {
        $this->assertReasonProvided($reason);

        DB::transaction(function () use ($company, $decidedBy, $reason) {
            $locked = $this->lockAndAssertStatus($company, CompanyStatus::Pending);

            $this->recordHistory($locked, CompanyStatus::Pending, CompanyStatus::Rejected, $reason, $decidedBy);

            $locked->status = CompanyStatus::Rejected->value;
            $locked->save();

            $this->notifyManagers($locked, CompanyStatus::Rejected, $reason, route('application.status', [], false));

            $company->setRawAttributes($locked->getAttributes(), true);
        });
    }

    /**
     * Suspend une compagnie active : active -> suspended.
     */
    public function suspend(Company $company, User $decidedBy, string $reason): void
    {
        $this->assertReasonProvided($reason);

        DB::transaction(function () use ($company, $decidedBy, $reason) {
            $locked = $this->lockAndAssertStatus($company, CompanyStatus::Active);

            $this->recordHistory($locked, CompanyStatus::Active, CompanyStatus::Suspended, $reason, $decidedBy);

            $locked->status = CompanyStatus::Suspended->value;
            $locked->save();

            $this->notifyManagers($locked, CompanyStatus::Suspended, $reason, route('application.status', [], false));

            $company->setRawAttributes($locked->getAttributes(), true);
        });
    }

    /**
     * Réactive une compagnie suspendue : suspended -> active.
     * Motif recommandé mais non obligatoire (ADR-005) — laissé NULL si
     * omis plutôt que remplacé par un texte générique, pour ne pas
     * fausser l'audit avec un motif qui n'a pas réellement été donné.
     */
    public function reactivate(Company $company, User $decidedBy, ?string $reason = null): void
    {
        DB::transaction(function () use ($company, $decidedBy, $reason) {
            $locked = $this->lockAndAssertStatus($company, CompanyStatus::Suspended);

            $this->recordHistory($locked, CompanyStatus::Suspended, CompanyStatus::Active, $reason, $decidedBy);

            $locked->status = CompanyStatus::Active->value;
            $locked->save();

            $this->notifyManagers($locked, CompanyStatus::Active, $reason, route('dashboard', [], false));

            $company->setRawAttributes($locked->getAttributes(), true);
        });
    }

    /**
     * Replace une demande refusée dans la file d'examen après correction.
     */
    public function resubmit(Company $company, User $submittedBy): void
    {
        DB::transaction(function () use ($company, $submittedBy) {
            $locked = $this->lockAndAssertStatus($company, CompanyStatus::Rejected);
            $issues = $this->resubmissionIssues($locked);

            if ($issues !== []) {
                throw new InvalidCompanyStatusTransitionException(implode(' ', $issues));
            }

            $this->recordHistory(
                $locked,
                CompanyStatus::Rejected,
                CompanyStatus::Pending,
                'Dossier corrigé et renvoyé pour examen',
                $submittedBy,
            );

            $locked->status = CompanyStatus::Pending->value;
            $locked->save();

            $recipients = User::query()
                ->where('status', 'active')
                ->whereIn('role', ['admin', 'platform_staff'])
                ->with('platformRole')
                ->get()
                ->filter(fn (User $user) => $user->permitsPlatform('companies.validate'));
            Notification::send($recipients, new CompanyApplicationResubmittedNotification(
                $locked->id,
                $locked->legal_name,
                route('companies.show', $locked, false),
            ));

            $company->setRawAttributes($locked->getAttributes(), true);
        });
    }

    /** @return array<int, string> */
    public function resubmissionIssues(Company $company): array
    {
        if (! $company->documents()->exists()) {
            return ['Ajoutez au moins un document administratif avant de renvoyer le dossier.'];
        }

        $issues = [];
        $requiredTypes = DocumentType::query()
            ->where('is_required', true)
            ->whereIn('applicable_to', ['company', 'both'])
            ->get(['id', 'label']);
        $submittedTypeIds = $company->documents()
            ->whereIn('document_type_id', $requiredTypes->pluck('id'))
            ->pluck('document_type_id')
            ->unique();
        $missingRequiredTypes = $requiredTypes->whereNotIn('id', $submittedTypeIds);

        if ($missingRequiredTypes->isNotEmpty()) {
            $issues[] = 'Document obligatoire manquant : '.$missingRequiredTypes->pluck('label')->implode(', ').'.';
        }
        if ($company->documents()->whereDate('valid_until', '<', today())->exists()) {
            $issues[] = 'Remplacez les documents expirés avant de renvoyer le dossier.';
        }
        if ($company->documents()->where('review_status', 'rejected')->exists()) {
            $issues[] = 'Corrigez tous les documents refusés avant de renvoyer le dossier.';
        }

        return $issues;
    }

    /**
     * ADR-005 : le motif est obligatoire pour reject() et suspend(). Le
     * typage `string` seul ne suffit pas, car une chaîne vide reste un
     * string valide en PHP ; ce garde-fou explicite évite l'écart.
     */
    private function assertReasonProvided(string $reason): void
    {
        if (trim($reason) === '') {
            throw new \InvalidArgumentException('Un motif est obligatoire pour cette transition.');
        }
    }

    private function assertDocumentsAllowValidation(Company $company): void
    {
        if (! $company->documents()->exists()) {
            throw new InvalidCompanyStatusTransitionException(
                'La compagnie doit fournir au moins un document administratif avant sa validation.'
            );
        }

        $requiredTypes = DocumentType::query()
            ->where('is_required', true)
            ->whereIn('applicable_to', ['company', 'both'])
            ->get(['id', 'label']);
        $submittedTypeIds = $company->documents()
            ->whereIn('document_type_id', $requiredTypes->pluck('id'))
            ->pluck('document_type_id')
            ->unique();
        $missingRequiredTypes = $requiredTypes->whereNotIn('id', $submittedTypeIds);

        if ($missingRequiredTypes->isNotEmpty()) {
            throw new InvalidCompanyStatusTransitionException(
                'Document obligatoire manquant : '.$missingRequiredTypes->pluck('label')->implode(', ').'.'
            );
        }

        if ($company->documents()->whereDate('valid_until', '<', today())->exists()) {
            throw new InvalidCompanyStatusTransitionException(
                'La compagnie possède un document expiré. Renouvelez-le avant la validation.'
            );
        }

        if ($company->documents()->where('review_status', 'rejected')->exists()) {
            throw new InvalidCompanyStatusTransitionException(
                'La compagnie possède un document refusé. Il doit être corrigé et validé avant la validation du dossier.'
            );
        }

        if ($company->documents()
            ->where(fn ($query) => $query->whereNull('review_status')->orWhere('review_status', '!=', 'approved'))
            ->exists()) {
            throw new InvalidCompanyStatusTransitionException(
                'Tous les documents transmis doivent être validés avant la validation de la compagnie.'
            );
        }
    }

    /**
     * Verrouille la compagnie et vérifie que son statut réel permet la
     * transition demandée afin d'éviter les décisions concurrentes.
     */
    private function lockAndAssertStatus(Company $company, CompanyStatus $expected): Company
    {
        /** @var Company $locked */
        $locked = Company::query()
            ->whereKey($company->getKey())
            ->lockForUpdate()
            ->firstOrFail();

        if ($locked->status !== $expected) {
            throw new InvalidCompanyStatusTransitionException(
                "Transition invalide : la compagnie #{$locked->id} est en statut '{$locked->status->value}', "
                ."'{$expected->value}' était attendu."
            );
        }

        return $locked;
    }

    private function recordHistory(
        Company $company,
        ?CompanyStatus $previousStatus,
        CompanyStatus $newStatus,
        ?string $reason,
        ?User $decidedBy
    ): void {
        CompanyStatusHistory::create([
            'company_id' => $company->id,
            'previous_status' => $previousStatus?->value,
            'new_status' => $newStatus->value,
            'reason' => $reason,
            'decided_by' => $decidedBy?->id,
            'created_at' => now(),
        ]);
    }

    private function notifyManagers(Company $company, CompanyStatus $status, ?string $reason, string $url): void
    {
        $recipients = $company->users()
            ->where('role', 'company_manager')
            ->where('status', 'active')
            ->get();

        Notification::send($recipients, new CompanyStatusChangedNotification(
            $company->id,
            $company->legal_name,
            $status,
            $reason,
            $url,
        ));
    }
}
