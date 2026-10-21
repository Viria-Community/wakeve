<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\DocumentType;
use App\Models\TransportMode;
use App\Rules\LocalityInProvince;
use App\Services\ApplicationSummaryPdf;
use App\Services\CompanyRegistrationService;
use App\Support\GeographyInput;
use App\Support\RccmInput;
use App\Support\StrongPassword;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class PublicRegistrationController extends Controller
{
    public function __construct(
        private readonly CompanyRegistrationService $registrationService,
        private readonly ApplicationSummaryPdf $summaryPdf,
    ) {}

    public function identification(): View
    {
        return view('onboarding.identification', ['data' => session('registration.identification', [])]);
    }

    public function storeIdentification(Request $request): RedirectResponse
    {
        GeographyInput::normalize($request);
        RccmInput::normalize($request);

        $data = $request->validate([
            'company_website' => ['nullable', 'max:0'],
            'legal_name' => ['required', 'string', 'max:150'],
            'trade_name' => ['nullable', 'string', 'max:150'],
            'rccm' => ['required', 'string', 'max:50', 'unique:companies,rccm'],
            'nif' => ['nullable', 'string', 'max:50'],
            'address' => ['nullable', 'string', 'max:255'],
            'province' => ['nullable', Rule::in(array_keys(config('geography')))],
            'city' => ['nullable', 'required_with:province', 'string', 'max:100', new LocalityInProvince],
            'company_email' => ['required', 'email', 'max:150', 'unique:companies,email'],
            'phone' => ['required', 'string', 'max:30'],
            'terms' => ['accepted'],
        ], [
            'rccm.required' => 'Le numéro RCCM est obligatoire.',
            'rccm.unique' => 'Ce numéro RCCM est déjà enregistré par une compagnie.',
        ]);

        unset($data['terms']);

        session(['registration.identification' => $data]);

        return redirect()->route('registration.manager');
    }

    public function manager(): View|RedirectResponse
    {
        if (! session()->has('registration.identification')) {
            return redirect()->route('registration.identification');
        }

        return view('onboarding.manager', ['data' => session('registration.manager', [])]);
    }

    public function storeManager(Request $request): RedirectResponse
    {
        if (! session()->has('registration.identification')) {
            return redirect()->route('registration.identification');
        }
        $data = $request->validate([
            'manager_name' => ['required', 'string', 'max:255'],
            'manager_email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', StrongPassword::rule()],
        ]);
        $data['password'] = Hash::make($data['password']);
        session(['registration.manager' => $data]);

        return redirect()->route('registration.modes');
    }

    public function modes(): View|RedirectResponse
    {
        if (! session()->has('registration.manager')) {
            return redirect()->route(session()->has('registration.identification') ? 'registration.manager' : 'registration.identification');
        }

        return view('onboarding.modes', [
            'transportModes' => TransportMode::query()->where('label', '!=', 'air')->orderBy('label')->get(),
            'selected' => session('registration.transport_mode_id'),
        ]);
    }

    public function storeModes(Request $request): RedirectResponse
    {
        if (! session()->has('registration.manager')) {
            return redirect()->route('registration.manager');
        }
        $data = $request->validate([
            'transport_mode_id' => [
                'required',
                'integer',
                Rule::exists('transport_modes', 'id')->where(fn ($query) => $query->where('label', '!=', 'air')),
            ],
        ], [
            'transport_mode_id.required' => 'Sélectionnez un mode de transport.',
            'transport_mode_id.exists' => 'Sélectionnez un mode de transport pris en charge par la plateforme.',
        ]);

        session(['registration.transport_mode_id' => $data['transport_mode_id']]);

        return redirect()->route('registration.documents');
    }

    public function documents(): View|RedirectResponse
    {
        if (! session()->has('registration.identification') || ! session()->has('registration.manager') || ! session()->has('registration.transport_mode_id')) {
            return redirect()->route('registration.identification');
        }

        return view('onboarding.documents', [
            'documentTypes' => DocumentType::query()->whereIn('applicable_to', ['company', 'both'])->orderBy('label')->get(),
        ]);
    }

    public function submit(Request $request): RedirectResponse
    {
        $identification = session('registration.identification');
        $manager = session('registration.manager');
        $transportModeId = session('registration.transport_mode_id');

        if (! $identification || ! $manager || ! $transportModeId) {
            return redirect()->route('registration.identification');
        }

        $request->validate([
            'documents' => ['required', 'array'],
            'documents.*.file' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'mimetypes:application/pdf,image/jpeg,image/png', 'max:2048'],
            'documents.*.issued_at' => ['nullable', 'date', 'before_or_equal:today'],
            'documents.*.valid_until' => ['nullable', 'date', 'after_or_equal:today'],
        ], [
            'documents.required' => 'Ajoutez au moins un document administratif.',
            'documents.*.file.file' => 'Le document n’a pas pu être téléversé. Vérifiez sa taille.',
            'documents.*.file.mimes' => 'Chaque document doit être au format PDF, JPG ou PNG.',
            'documents.*.file.mimetypes' => 'Le contenu du fichier ne correspond pas à un format PDF, JPG ou PNG autorisé.',
            'documents.*.file.max' => 'Chaque document doit peser au maximum 2 Mo.',
            'documents.*.issued_at.before_or_equal' => 'La date de délivrance ne peut pas être dans le futur.',
            'documents.*.valid_until.after_or_equal' => 'Le document doit encore être valide aujourd’hui.',
        ]);

        $submittedDocuments = collect($request->file('documents', []))->filter(fn ($document) => isset($document['file']));
        if ($submittedDocuments->isEmpty()) {
            throw ValidationException::withMessages(['documents' => 'Ajoutez au moins un document administratif.']);
        }

        $missingRequiredTypes = $this->registrationService->missingRequiredDocuments($submittedDocuments->all());

        if ($missingRequiredTypes->isNotEmpty()) {
            throw ValidationException::withMessages(
                $missingRequiredTypes->mapWithKeys(fn (DocumentType $type) => [
                    "documents.{$type->id}.file" => "Le document « {$type->label} » est obligatoire pour soumettre la demande.",
                ])->all()
            );
        }

        foreach ($submittedDocuments as $typeId => $document) {
            if (! DocumentType::query()->whereKey($typeId)->whereIn('applicable_to', ['company', 'both'])->exists()) {
                throw ValidationException::withMessages(['documents' => 'Un type de document sélectionné est invalide.']);
            }

            if (! $request->filled("documents.$typeId.valid_until")) {
                throw ValidationException::withMessages([
                    "documents.$typeId.valid_until" => 'La date de fin de validité est obligatoire pour chaque document ajouté.',
                ]);
            }
        }

        $throttleKey = 'registration|'.$request->ip();
        if (RateLimiter::tooManyAttempts($throttleKey, 3)) {
            throw ValidationException::withMessages(['documents' => 'Trop de demandes ont été soumises. Réessayez plus tard.']);
        }

        $documents = $submittedDocuments->mapWithKeys(fn ($document, $typeId) => [$typeId => [
            'file' => $document['file'],
            'issued_at' => $request->input("documents.$typeId.issued_at"),
            'valid_until' => $request->input("documents.$typeId.valid_until"),
        ]])->all();

        Validator::make(array_merge($identification, $manager), [
            'company_email' => ['unique:companies,email'], 'manager_email' => ['unique:users,email'],
        ])->validate();

        $company = $this->registrationService->register(array_merge($identification, $manager), (int) $transportModeId, $documents);
        $reference = $company->application_reference;

        RateLimiter::hit($throttleKey, 3600);
        session()->forget('registration');
        session(['registration.completed_reference' => $reference]);

        return redirect()->route('registration.success', $reference);
    }

    public function success(string $reference): View
    {
        $company = $this->completedCompany($reference);

        return view('onboarding.success', compact('company'));
    }

    public function summary(string $reference): Response
    {
        $company = $this->completedCompany($reference);

        return response($this->summaryPdf->generate($company), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="recapitulatif-'.$reference.'.pdf"',
        ]);
    }

    private function completedCompany(string $reference): Company
    {
        abort_unless(hash_equals((string) session('registration.completed_reference'), $reference), 403);

        return Company::query()->where('application_reference', $reference)->firstOrFail();
    }
}
