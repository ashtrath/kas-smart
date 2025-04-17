<?php

namespace App\Enum;

use Filament\Support\Contracts\HasLabel;

enum PaymentMethodType: string implements HasLabel
{
    case CASH = 'cash';
    case E_WALLET = 'e-wallet';
    case BANK = 'bank';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::CASH => __('enum.payment_method_type.cash'),
            self::E_WALLET => __('enum.payment_method_type.e-wallet'),
            self::BANK => __('enum.payment_method_type.bank'),
        };
    }
}
