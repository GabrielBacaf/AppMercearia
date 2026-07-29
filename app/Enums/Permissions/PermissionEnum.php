<?php

namespace App\Enums\Permissions;

enum PermissionEnum :string
{
    case INDEX = 'listar permissoes';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
