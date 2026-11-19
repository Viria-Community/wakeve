<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class DocumentRejectedNotification extends Notification
{
    use Queueable;

    public function __construct(
        private readonly int $documentId,
        private readonly string $documentType,
        private readonly string $documentScope,
        private readonly string $reason,
        private readonly string $url,
    ) {}

    /** @return array<int, string> */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Un document de votre dossier doit être corrigé')
            ->greeting('Bonjour,')
            ->line("Le document « {$this->documentType} » de votre dossier a été refusé.")
            ->line("Motif : {$this->reason}")
            ->line('Téléversez un nouveau document, puis demandez un nouvel examen depuis votre espace.')
            ->action('Voir mon dossier', $this->url)
            ->salutation('Plateforme Nationale de Mobilité du Gabon');
    }

    /** @return array<string, int|string> */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Document refusé',
            'message' => "Le document « {$this->documentType} » doit être corrigé.",
            'reason' => $this->reason,
            'document_id' => $this->documentId,
            'document_scope' => $this->documentScope,
            'url' => $this->url,
        ];
    }
}
