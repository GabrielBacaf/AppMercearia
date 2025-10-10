<?php

namespace App\Enums;

enum StatusEnum: string
{
    case PENDENTE = 'PENDENTE';

    case FINALIZADO = 'FINALIZADO';

    case CANCELADO = 'CANCELADO';

    case ERRORESTOQUE = 'Erro no cadastro de Estoque';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}

