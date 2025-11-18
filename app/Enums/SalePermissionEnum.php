<?php
namespace App\Enums;

enum SalePermissionEnum: string
{
    case STORE = 'store sale';
    case UPDATE = 'update sale';
    case SHOW = 'show sale';
    case INDEX = 'index sale';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
