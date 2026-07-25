<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ReceivableDueTodayNotification extends Notification
{
    use Queueable;

    public function __construct(public \App\Models\AccountReceivable $receivable) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'message' => 'Lembrete: Conta a receber vence HOJE: ' . $this->receivable->title,
            'receivable_id' => $this->receivable->id,
            'amount' => $this->receivable->amount,
            'due_date' => $this->receivable->due_date,
        ];
    }
}
