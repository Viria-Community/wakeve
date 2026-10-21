<?php

namespace App\Http\Controllers;

use App\Enums\CompanyStatus;
use App\Exceptions\InvalidCompanyStatusTransitionException;
use App\Models\DocumentType;
use App\Services\CompanyStatusService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class ApplicationStatusController extends Controller
{
    public function __invoke(CompanyStatusService $statusService): View|RedirectResponse
    {
        $user = auth()->user();
        abort_unless($user->status === 'active' && $user->permits('companies.view'), 403);

        abort_unless($user->role === 'company_manager' && $user->company_id !== null, 403);

        if ($user->company?->status === CompanyStatus::Active) {
            return redirect()->route('dashboard');
        }

        $company = $user->company->load(['documents.documentType', 'statusHistory.decidedByUser']);
        $statusDecision = $company->statusHistory
            ->first(fn ($history) => $history->new_status === $company->status->value);

        return view('onboarding.status', [
            'user' => $user,
            'company' => $company,
            'statusDecision' => $statusDecision,
            'resubmissionIssues' => $company->status === CompanyStatus::Rejected
                ? $statusService->resubmissionIssues($company)
                : [],
            'documentTypes' => DocumentType::query()
                ->whereIn('applicable_to', ['company', 'both'])
                ->whereDoesntHave('companyDocuments', fn ($query) => $query->where('company_id', $company->id))
                ->orderBy('label')
                ->get(),
        ]);
    }

    public function resubmit(Request $request, CompanyStatusService $statusService): RedirectResponse
    {
        $user = $request->user();
        abort_unless($user?->status === 'active' && $user->role === 'company_manager' && $user->company_id !== null, 403);
        abort_unless($user->permits('companies.update'), 403);

        try {
            $statusService->resubmit($user->company, $user);
        } catch (InvalidCompanyStatusTransitionException $exception) {
            throw ValidationException::withMessages(['application' => $exception->getMessage()]);
        }

        return redirect()->route('application.status')
            ->with('status', 'Votre dossier corrigé a été renvoyé à l’administration pour un nouvel examen.');
    }
}
