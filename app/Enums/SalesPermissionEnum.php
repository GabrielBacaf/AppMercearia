<?php

namespace App\Enums;

enum SalesPermissionEnum: string
{
    case CREATE = 'create sale';
    case UPDATE = 'update sale';
    case DESTROY = 'destroy sale';

    /**
     * Retorna todas as permissões em array de strings
     */
    public static function values(): array
    {
        return array_map(fn(self $case) => $case->value, self::cases());
    }
}
