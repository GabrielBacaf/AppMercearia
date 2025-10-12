<?php

namespace App\Enums;

enum PaymentTypeEnum: string
{
    case DINHEIRO = 'Dinheiro';
    case PIXEMPRESA = 'Pix Empresa';
    case PIXQRCODE = 'Pix Qrcode';
    case PIXMAE = 'Pix Mãe';
    case PIXPAI = 'Pix Pai';
    case CREDITO = 'Cartão de Crédito';
    case DEBITO = 'Cartão de Débito';
    case FIADO = 'Fiado';
    case COMSUMO = 'Consumo Pessoal';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
