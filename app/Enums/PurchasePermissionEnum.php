<?php

namespace App\Enums;

enum PurchasePermissionEnum: string
{
    case STORE = 'store purchase';
    case UPDATE = 'update purchase';
    case DESTROY = 'destroy purchase';
    case SHOW = 'show purchase';
    case INDEX = 'index purchase';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
