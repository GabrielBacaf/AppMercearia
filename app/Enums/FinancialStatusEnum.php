<?php

namespace App\Enums;

enum FinancialStatusEnum: string
{
    case PENDING = 'pending';
    case PAID = 'paid';
    case PARTIALLY_PAID = 'partially_paid';
    case RECEIVED = 'received';
    case OVERDUE = 'overdue';

    public function label(): string
    {
        return match($this) {
            self::PENDING => 'Pendente',
            self::PARTIALLY_PAID => 'Parcialmente Pago',
            self::PAID => 'Pago',
            self::RECEIVED => 'Recebido',
            self::OVERDUE => 'Atrasado',
        };
    }
}
