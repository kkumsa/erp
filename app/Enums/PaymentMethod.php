<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum PaymentMethod: string implements HasLabel
{
    case Cash = 'cash';
    case Card = 'card';
    case BankTransfer = 'bank_transfer';
    case Check = 'check';
    case Other = 'other';

    public function getLabel(): string
    {
        return __("enums.payment_method.{$this->value}");
    }
}
