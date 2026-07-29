<?php

namespace App\Enums\Permissions;

enum ClientPermissionEnum: string
{
    case STORE = 'criar cliente';
    case UPDATE = 'editar cliente';
    case DESTROY = 'deletar cliente';
    case SHOW = 'visualizar cliente';
    case INDEX = 'listar clientes';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
