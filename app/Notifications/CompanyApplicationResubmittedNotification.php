<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class CompanyApplicationResubmittedNotification extends Notification
{
    use Queueable;

    public function __construct(
        private readonly int $companyId,
        private readonly string $companyName,
        private readonly string $url,
    ) {}

    /** @return array<int, string> */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /** @return array<string, int|string> */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Dossier corrigé à examiner',
            'message' => "{$this->companyName} a renvoyé sa demande après correction.",
            'company_id' => $this->companyId,
            'url' => $this->url,
        ];
    }
}
