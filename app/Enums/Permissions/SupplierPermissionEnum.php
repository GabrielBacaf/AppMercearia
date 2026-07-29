<?php

namespace App\Enums\Permissions;

enum SupplierPermissionEnum : string
{
    case STORE = 'criar fornecedor';
    case UPDATE = 'editar fornecedor';
    case DESTROY = 'deletar fornecedor';
    case SHOW = 'visualizar fornecedor';
    case INDEX = 'listar fornecedores';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
