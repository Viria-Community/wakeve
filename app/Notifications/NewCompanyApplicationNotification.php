<?php

namespace App\Notifications;

use App\Models\Company;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewCompanyApplicationNotification extends Notification
{
    use Queueable;

    public function __construct(private Company $company)
    {
    }

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Nouvelle demande d’enregistrement : '.$this->company->legal_name)
            ->greeting('Bonjour,')
            ->line('La compagnie '.$this->company->legal_name.' vient de soumettre sa demande d’enregistrement.')
            ->line('Elle est en attente d’examen administratif.')
            ->action('Examiner la demande', url('/companies/'.$this->company->id))
            ->salutation('Plateforme Nationale de Mobilité du Gabon');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Nouvelle demande',
            'message' => 'La compagnie '.$this->company->legal_name.' a soumis sa demande.',
            'url' => '/companies/'.$this->company->id,
        ];
    }
}
