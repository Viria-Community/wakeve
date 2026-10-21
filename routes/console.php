<?php

use App\Models\AuditLog;
use App\Models\CompanyDocument;
use App\Models\FleetDocument;
use App\Services\DocumentStatusService;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('documents:refresh-statuses', function (DocumentStatusService $statusService) {
    $updated = 0;

    foreach ([CompanyDocument::class, FleetDocument::class] as $model) {
        $model::query()->eachById(function ($document) use ($statusService, &$updated) {
            $status = $statusService->forDate($document->valid_until);

            if ($document->status !== $status) {
                $document->update(['status' => $status]);
                $updated++;
            }
        });
    }

    $this->info("{$updated} statut(s) documentaire(s) mis à jour.");
})->purpose('Actualiser les statuts des documents selon leur échéance');

Schedule::command('documents:refresh-statuses')->dailyAt('00:10');

Artisan::command('audit-logs:purge', function () {
    $deleted = AuditLog::query()
        ->where('created_at', '<', now()->subMonthsNoOverflow(6))
        ->delete();

    $this->info("{$deleted} entrée(s) du journal antérieure(s) à six mois supprimée(s).");
})->purpose('Supprimer les entrées du journal d’activité âgées de plus de six mois');

Schedule::command('audit-logs:purge')->dailyAt('00:30');
