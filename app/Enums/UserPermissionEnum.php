<?php

namespace App\Enums;

enum UserPermissionEnum:string
{
    case CREATE = 'create user';
    case READ = 'read user';
    case UPDATE = 'update user';
    case DESTROY = 'destroy user';
    case SHOW = 'show user';
    case INDEX = 'index user';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
