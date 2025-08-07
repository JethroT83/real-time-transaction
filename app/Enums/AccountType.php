<?php

namespace App\Enums;

enum AccountType: string
{
    case CHECKING = 'checking';
    case SAVINGS = 'savings';
    case CREDIT = 'credit';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function random(): self
    {
        return self::cases()[array_rand(self::cases())];
    }
}
