<?php

namespace App\Enums;

enum SupplierPermissionEnum : string
{
    case STORE = 'store supplier';
    case UPDATE = 'update supplier';
    case DESTROY = 'destroy supplier';
    case SHOW = 'show supplier';
    case INDEX = 'index supplier';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
