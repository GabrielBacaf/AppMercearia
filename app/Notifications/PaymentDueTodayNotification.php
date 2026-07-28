<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class PaymentDueTodayNotification extends Notification
{
    use Queueable;

    public function __construct(public \App\Models\AccountPayable $payable) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'message' => 'Lembrete: Conta a pagar vence HOJE: ' . $this->payable->title,
            'payable_id' => $this->payable->id,
            'amount' => $this->payable->amount,
            'due_date' => $this->payable->due_date,
        ];
    }
}
