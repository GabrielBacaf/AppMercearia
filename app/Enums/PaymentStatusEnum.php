<?php

namespace App\Enums;

enum PaymentStatusEnum: string
{
    case PAGO = 'Pago';

    case DEVENDO = 'Devendo';

    case CONSUMO = 'Consumo';

    public static function values(): array
    {
        return array_map(fn(self $case) => $case->value, self::cases());
    }

}
