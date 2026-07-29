<?php

namespace App\Enums\Permissions;

enum UserPermissionEnum:string
{
    case STORE = 'criar usuario';
    case UPDATE = 'editar usuario';
    case DESTROY = 'deletar usuario';
    case SHOW = 'visualizar usuario';
    case INDEX = 'listar usuarios';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
