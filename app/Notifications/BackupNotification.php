<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class BackupNotification extends Notification
{
    use Queueable;

    public function via($notifiable)
    {
        return ['mail']; // Change this if using Slack, database, etc.
    }

    public function toMail($notifiable)
    {
        return (new \Illuminate\Notifications\Messages\MailMessage)
            ->subject('Backup Completed')
            ->line('Your system backup was successfully created.');
    }
}

