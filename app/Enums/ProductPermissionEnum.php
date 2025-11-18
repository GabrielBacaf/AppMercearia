<?php

namespace App\Enums;

enum ProductPermissionEnum :string
{
    case STORE = 'store product';
    case UPDATE = 'update product';
    case DESTROY = 'destroy product';
    case SHOW = 'show product';
    case INDEX = 'index product';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
