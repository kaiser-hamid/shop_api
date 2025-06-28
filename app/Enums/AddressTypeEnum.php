<?php

namespace App\Enums;

enum AddressTypeEnum: string
{
    case DEFAULT = 'default';
    case SHIPPING = 'shipping';
    case BILLING = 'billing';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}