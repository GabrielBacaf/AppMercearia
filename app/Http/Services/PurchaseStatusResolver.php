<?php

namespace App\Http\Services;

use App\Enums\StatusEnum;

class PurchaseStatusResolver
{
    /**
     * Resolve the Purchase status based on its financial balance and pending payments.
     */
    public static function resolve(float $countValue, bool $hasPendingPayments): string
    {
        return match (true) {
            $countValue == 0 && !$hasPendingPayments => StatusEnum::FINALIZADO->value,
            $countValue == 0 && $hasPendingPayments  => StatusEnum::PAGAMENTO_PENDENTE->value,
            $countValue < 0                          => StatusEnum::ERRO_ESTOQUE->value,
            default                                  => StatusEnum::PENDENTE->value,
        };
    }
}
