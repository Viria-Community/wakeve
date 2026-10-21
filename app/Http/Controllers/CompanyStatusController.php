<?php

namespace App\Http\Controllers;

use App\Exceptions\InvalidCompanyStatusTransitionException;
use App\Http\Requests\Company\ChangeCompanyStatusRequest;
use App\Models\Company;
use App\Services\CompanyStatusService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Validation\ValidationException;

class CompanyStatusController extends Controller
{
    public function __invoke(
        ChangeCompanyStatusRequest $request,
        Company $company,
        CompanyStatusService $statusService
    ): RedirectResponse {
        try {
            match ($request->validated('action')) {
                'validate' => $statusService->validateAndActivate($company, $request->user()),
                'reject' => $statusService->reject($company, $request->user(), $request->validated('reason')),
                'suspend' => $statusService->suspend($company, $request->user(), $request->validated('reason')),
                'reactivate' => $statusService->reactivate($company, $request->user(), $request->validated('reason')),
            };
        } catch (InvalidCompanyStatusTransitionException $exception) {
            throw ValidationException::withMessages(['action' => $exception->getMessage()]);
        }

        return redirect()->route('companies.show', $company)
            ->with('status', 'Le statut de la compagnie a été mis à jour.');
    }
}
