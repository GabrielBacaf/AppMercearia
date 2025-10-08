<?php

namespace App\Enums;

enum RolePermissionEnum: string
{
    case CREATE = 'create role';
    case READ = 'read role';
    case UPDATE = 'update role';
    case DESTROY = 'destroy role';
    case SHOW = 'show role';
    case INDEX = 'index role';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
