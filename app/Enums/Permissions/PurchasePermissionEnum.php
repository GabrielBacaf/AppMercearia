<?php

namespace App\Enums\Permissions;

enum PurchasePermissionEnum: string
{
    case STORE = 'criar compra';
    case UPDATE = 'editar compra';
    case DESTROY = 'deletar compra';
    case SHOW = 'visualizar compra';
    case INDEX = 'listar compras';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
