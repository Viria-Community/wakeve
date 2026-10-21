<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
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
        return ['database'];
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
