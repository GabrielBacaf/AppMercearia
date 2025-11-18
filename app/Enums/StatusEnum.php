<?php

namespace App\Enums;

enum StatusEnum: string
{
    case PENDENTE = 'PENDENTE';


    case FINALIZADO = 'FINALIZADO';

    case PAGAMENTO_PENDENTE = 'PAGAMENTO PENDENTE';

    case CANCELADO = 'CANCELADO';

    case ERRO_ESTOQUE = 'Valor Produtos Maiores - Cadastrado na Compra';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}

