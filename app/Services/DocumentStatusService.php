<?php

namespace App\Services;

use Carbon\CarbonInterface;

class DocumentStatusService
{
    public function forDate(CarbonInterface $validUntil): string
    {
        $today = now()->startOfDay();
        $deadline = $validUntil->copy()->startOfDay();

        if ($deadline->lt($today)) {
            return 'expired';
        }

        return $deadline->lte($today->copy()->addDays(30)) ? 'to_renew' : 'valid';
    }
}
