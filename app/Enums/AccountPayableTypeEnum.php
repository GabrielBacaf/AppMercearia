<?php

namespace App\Enums;

enum AccountPayableTypeEnum: string
{
    case OPERATIONAL = 'operational';
    case SUPPLIER = 'supplier';
    case TAX = 'tax';
    case MAINTENANCE = 'maintenance';
    case OTHERS = 'others';

    public function label(): string
    {
        return match($this) {
            self::OPERATIONAL => 'Operacional',
            self::SUPPLIER => 'Fornecedor',
            self::TAX => 'Imposto',
            self::MAINTENANCE => 'Manutenção',
            self::OTHERS => 'Outros',
        };
    }
}
