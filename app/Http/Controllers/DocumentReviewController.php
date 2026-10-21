<?php

namespace App\Http\Controllers;

use App\Models\CompanyDocument;
use App\Models\FleetDocument;
use App\Notifications\DocumentRejectedNotification;
use App\Services\AuditLogger;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class DocumentReviewController extends Controller
{
    public function company(Request $request, CompanyDocument $companyDocument): RedirectResponse
    {
        return $this->review($request, $companyDocument);
    }

    public function fleet(Request $request, FleetDocument $fleetDocument): RedirectResponse
    {
        return $this->review($request, $fleetDocument);
    }

    private function review(Request $request, Model $document): RedirectResponse
    {
        abort_unless($request->user()->permitsPlatform('documents.validate'), 403);
        $data = $request->validate([
            'decision' => ['required', Rule::in(['approved', 'rejected'])],
            'comment' => [Rule::requiredIf($request->input('decision') === 'rejected'), 'nullable', 'string', 'max:1000'],
        ]);
        if ($document->review_status !== 'pending') {
            throw ValidationException::withMessages([
                'decision' => 'Ce document a déjà été contrôlé. Il doit être modifié ou remplacé avant une nouvelle décision.',
            ]);
        }
        if (! Storage::disk('local')->exists($document->file_path)) {
            throw ValidationException::withMessages([
                'decision' => 'Le fichier associé est introuvable. Demandez son remplacement avant de poursuivre.',
            ]);
        }
        $document->update([
            'review_status' => $data['decision'],
            'reviewed_by' => $request->user()->id,
            'reviewed_at' => now(),
            'review_comment' => $data['comment'] ?? null,
        ]);
        AuditLogger::record('validation_document', $data['decision'] === 'approved' ? 'Document validé' : 'Document refusé', $document, ['review_status' => $data['decision']]);

        if ($data['decision'] === 'rejected') {
            $company = $document instanceof CompanyDocument
                ? $document->company
                : $document->fleetUnit->company;
            $scope = $document instanceof CompanyDocument ? 'company' : 'fleet';
            $url = $company->status->value === 'active'
                ? route('documents.index', [], false).'#document-'.$scope.'-'.$document->id
                : route('application.status', [], false).'#document-'.$document->id;
            $recipients = $company->users()
                ->where('role', 'company_manager')
                ->where('status', 'active')
                ->get();

            Notification::send($recipients, new DocumentRejectedNotification(
                $document->id,
                $document->documentType->label,
                $scope,
                $data['comment'],
                $url,
            ));
        }

        return back()->with('status', $data['decision'] === 'approved' ? 'Le document a été validé.' : 'Le document a été refusé.');
    }
}
