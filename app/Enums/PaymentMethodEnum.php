<?php

namespace App\Enums;

enum PaymentMethodEnum: string
{
    case COD = 'cod';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}