<?php

namespace App\Notifications;

use App\Enums\CompanyStatus;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
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
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $mail = (new MailMessage)
            ->greeting('Bonjour,')
            ->salutation('Plateforme Nationale de Mobilité du Gabon');

        return match ($this->status) {
            CompanyStatus::Rejected => $mail->subject('Votre demande d’enregistrement a été refusée')
                ->line("La demande d’enregistrement de {$this->companyName} a été refusée.")
                ->line($this->reason ? "Motif : {$this->reason}" : 'Aucun motif n’a été précisé.')
                ->action('Voir mon dossier', $this->url),
            CompanyStatus::Active => $mail->subject('Votre compagnie est validée')
                ->line("{$this->companyName} est validée et active sur la plateforme.")
                ->line('Vous pouvez désormais gérer vos agences, votre flotte et votre offre de transport.')
                ->action('Accéder à mon espace', $this->url),
            CompanyStatus::Suspended => $mail->subject('Accès opérationnel suspendu')
                ->line("L’accès opérationnel de {$this->companyName} a été suspendu.")
                ->action('Voir mon espace', $this->url),
            default => $mail->subject('Mise à jour de votre dossier')
                ->line("Le statut de {$this->companyName} a été mis à jour.")
                ->action('Voir mon espace', $this->url),
        };
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
