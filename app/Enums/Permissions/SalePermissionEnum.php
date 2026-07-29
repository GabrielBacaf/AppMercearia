<?php
namespace App\Enums\Permissions;

enum SalePermissionEnum: string
{
    case STORE = 'criar venda';
    case UPDATE = 'editar venda';
    case SHOW = 'visualizar venda';
    case INDEX = 'listar vendas';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
