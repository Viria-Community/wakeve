<?php

namespace App\Services;

use App\Models\Company;
use App\Models\DocumentType;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use League\Flysystem\FilesystemException;
use Throwable;

/** Orchestre atomiquement l'inscription publique d'une compagnie (ADR-006). */
class CompanyRegistrationService
{
    public function __construct(private readonly DocumentStatusService $statusService) {}

    /**
     * @param  array<string, mixed>  $identification
     * @param  array<int|string, array{file: UploadedFile, issued_at?: string|null, valid_until: string}>  $documents
     */
    public function register(array $identification, int $transportModeId, array $documents): Company
    {
        $this->assertRequiredDocumentsPresent($documents);

        $storedPaths = [];

        try {
            return DB::transaction(function () use ($identification, $transportModeId, $documents, &$storedPaths): Company {
                $company = Company::create([
                    'application_reference' => $this->newReference(),
                    'legal_name' => $identification['legal_name'],
                    'trade_name' => $identification['trade_name'] ?? null,
                    'rccm' => $identification['rccm'],
                    'nif' => $identification['nif'] ?? null,
                    'email' => $identification['company_email'],
                    'phone' => $identification['phone'],
                    'address' => $identification['address'] ?? null,
                    'city' => $identification['city'] ?? null,
                    'province' => $identification['province'] ?? null,
                ]);

                $company->transportModes()->sync([$transportModeId]);
                app(HeadquartersService::class)->create($company);

                User::create([
                    'name' => $identification['manager_name'],
                    'email' => $identification['manager_email'],
                    'password' => $identification['password'],
                    'role' => 'company_manager',
                    'company_id' => $company->id,
                    'status' => 'active',
                ]);

                foreach ($documents as $typeId => $document) {
                    $validUntil = Carbon::parse($document['valid_until']);
                    $path = $document['file']->store("documents/companies/{$company->id}", 'local');

                    if (! is_string($path) || $path === '') {
                        throw ValidationException::withMessages([
                            "documents.$typeId.file" => 'Le document n’a pas pu être enregistré. Réessayez ou contactez l’administrateur.',
                        ]);
                    }

                    $storedPaths[] = $path;
                    $company->documents()->create([
                        'document_type_id' => $typeId,
                        'file_path' => $path,
                        'issued_at' => $document['issued_at'] ?? null,
                        'valid_until' => $validUntil,
                        'status' => $this->statusService->forDate($validUntil),
                    ]);
                }

                return $company;
            });
        } catch (Throwable $exception) {
            foreach ($storedPaths as $path) {
                Storage::disk('local')->delete($path);
            }

            if ($exception instanceof FilesystemException) {
                report($exception);

                throw ValidationException::withMessages([
                    'documents' => 'Les documents n’ont pas pu être enregistrés. Réessayez ou contactez l’administrateur.',
                ]);
            }

            throw $exception;
        }
    }

    /**
     * @param  array<int|string, mixed>  $documents
     * @return Collection<int, DocumentType>
     */
    public function missingRequiredDocuments(array $documents): Collection
    {
        $requiredTypes = DocumentType::query()
            ->where('is_required', true)
            ->whereIn('applicable_to', ['company', 'both'])
            ->get(['id', 'label']);

        $submittedTypeIds = collect(array_keys($documents))->map(fn ($id) => (int) $id);

        return $requiredTypes->whereNotIn('id', $submittedTypeIds);
    }

    /**
     * @param  array<int|string, mixed>  $documents
     */
    private function assertRequiredDocumentsPresent(array $documents): void
    {
        $missingRequiredTypes = $this->missingRequiredDocuments($documents);

        if ($missingRequiredTypes->isNotEmpty()) {
            throw ValidationException::withMessages([
                'documents' => 'Document obligatoire manquant : '.$missingRequiredTypes->pluck('label')->implode(', ').'.',
            ]);
        }
    }

    private function newReference(): string
    {
        do {
            $reference = 'REF-GAB-'.now()->format('Y').'-'.Str::upper(Str::random(8));
        } while (Company::query()->where('application_reference', $reference)->exists());

        return $reference;
    }
}
