<?php

namespace App\Enums;


enum ClientPermissionEnum: string
{
    case STORE = 'store client';
    case UPDATE = 'update client';
    case DESTROY = 'destroy client';
    case SHOW = 'show client';
    case INDEX = 'index client';


    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
