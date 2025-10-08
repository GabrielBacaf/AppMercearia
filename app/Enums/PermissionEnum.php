<?php

namespace App\Enums;

enum PermissionEnum :string
{
    case INDEX = 'index permission';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
