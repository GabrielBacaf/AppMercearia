<?php

namespace App\Enums;

enum UserPermissionEnum:string
{
    case CREATE = 'create users';
    case READ = 'read users';
    case UPDATE = 'update users';
    case DELETE = 'delete users';
    case SHOW = 'show users';
    case LIST = 'list users';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
