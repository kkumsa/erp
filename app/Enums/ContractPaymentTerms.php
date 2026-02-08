<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum ContractPaymentTerms: string implements HasLabel
{
    case LumpSum = 'lump_sum';
    case Installment = 'installment';
    case Monthly = 'monthly';
    case Milestone = 'milestone';

    public function getLabel(): string
    {
        return __("enums.contract_payment_terms.{$this->value}");
    }
}
