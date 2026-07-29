<?php

namespace App\Enums\Permissions;

enum RolePermissionEnum: string
{
    case STORE = 'criar cargo';
    case UPDATE = 'editar cargo';
    case DESTROY = 'deletar cargo';
    case SHOW = 'visualizar cargo';
    case INDEX = 'listar cargos';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
