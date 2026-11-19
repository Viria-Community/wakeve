<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
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
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $data = $this->toArray($notifiable);

        return (new MailMessage)
            ->subject('Un dossier a été renvoyé pour examen')
            ->greeting('Bonjour,')
            ->line($data['message'] ?? 'Un dossier corrigé a été renvoyé pour examen.')
            ->action('Examiner le dossier', $data['url'] ?? url('/'))
            ->salutation('Plateforme Nationale de Mobilité du Gabon');
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
