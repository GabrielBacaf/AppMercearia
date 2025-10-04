<?php

namespace App\Enums;

enum SalesPermissionEnum: string
{
    case CREATE = 'create sales';
    case READ = 'read sales';
    case UPDATE = 'update sales';
    case DESTROY = 'destroy sales';

    /**
     * Retorna todas as permissões em array de strings
     */
    public static function values(): array
    {
        return array_map(fn(self $case) => $case->value, self::cases());
    }
}
