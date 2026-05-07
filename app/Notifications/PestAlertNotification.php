<?php

namespace App\Notifications;

use App\Models\PestAlert;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\BroadcastMessage;

class PestAlertNotification extends Notification
{
    use Queueable;

    protected $alert;

    public function __construct(PestAlert $alert)
    {
        $this->alert = $alert;
    }

    public function via($notifiable)
    {
        return ['database', 'broadcast', 'mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Pest Alert: ' . $this->alert->title)
            ->line($this->alert->description)
            ->line('Location: ' . $this->alert->location)
            ->line('Severity: ' . ucfirst($this->alert->severity))
            ->action('View Alert', url('/pest-alerts/' . $this->alert->id))
            ->line('Please take necessary action.');
    }

    public function toArray($notifiable)
    {
        return [
            'alert_id' => $this->alert->id,
            'title' => $this->alert->title,
            'description' => $this->alert->description,
            'severity' => $this->alert->severity,
            'location' => $this->alert->location,
            'pest_type' => $this->alert->pest_type,
        ];
    }

    public function toBroadcast($notifiable)
    {
        return new BroadcastMessage([
            'alert_id' => $this->alert->id,
            'title' => $this->alert->title,
            'description' => $this->alert->description,
            'severity' => $this->alert->severity,
            'location' => $this->alert->location,
            'pest_type' => $this->alert->pest_type,
        ]);
    }
} 