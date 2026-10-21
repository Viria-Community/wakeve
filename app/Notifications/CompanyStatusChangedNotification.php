<?php

namespace App\Notifications;

use App\Enums\CompanyStatus;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class CompanyStatusChangedNotification extends Notification
{
    use Queueable;

    public function __construct(
        private readonly int $companyId,
        private readonly string $companyName,
        private readonly CompanyStatus $status,
        private readonly ?string $reason,
        private readonly string $url,
    ) {}

    /** @return array<int, string> */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /** @return array<string, int|string|null> */
    public function toArray(object $notifiable): array
    {
        [$title, $message] = match ($this->status) {
            CompanyStatus::Rejected => [
                'Demande refusée',
                "La demande d’enregistrement de {$this->companyName} a été refusée.",
            ],
            CompanyStatus::Suspended => [
                'Compagnie suspendue',
                "L’accès opérationnel de {$this->companyName} a été suspendu.",
            ],
            CompanyStatus::Active => [
                'Compagnie réactivée',
                "L’accès opérationnel de {$this->companyName} a été rétabli.",
            ],
            default => [
                'Statut de la compagnie mis à jour',
                "Le statut de {$this->companyName} a été mis à jour.",
            ],
        };

        return [
            'title' => $title,
            'message' => $message,
            'reason' => $this->reason,
            'company_id' => $this->companyId,
            'company_status' => $this->status->value,
            'url' => $this->url,
        ];
    }
}
