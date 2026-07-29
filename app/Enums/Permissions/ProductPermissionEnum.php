<?php

namespace App\Enums\Permissions;

enum ProductPermissionEnum :string
{
    case STORE = 'criar produto';
    case UPDATE = 'editar produto';
    case DESTROY = 'deletar produto';
    case SHOW = 'visualizar produto';
    case INDEX = 'listar produtos';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
