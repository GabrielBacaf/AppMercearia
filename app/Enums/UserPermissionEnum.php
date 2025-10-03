<?php

namespace App\Enums;

enum UserPermissionEnum:string
{
    case CREATE = 'create users';
    case READ = 'read users';
    case UPDATE = 'update users';
    case DESTROY = 'destroy users';
    case SHOW = 'show users';
    case INDEX = 'index users';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
