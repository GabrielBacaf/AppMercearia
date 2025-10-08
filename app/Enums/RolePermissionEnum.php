<?php

namespace App\Enums;

enum RolePermissionEnum: string
{
    case STORE = 'store role';
    case UPDATE = 'update role';
    case DESTROY = 'destroy role';
    case SHOW = 'show role';
    case INDEX = 'index role';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
