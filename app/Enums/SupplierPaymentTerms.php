<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum SupplierPaymentTerms: string implements HasLabel
{
    case Prepaid = 'prepaid';
    case Postpaid = 'postpaid';
    case Settlement = 'settlement';

    public function getLabel(): string
    {
        return __("enums.supplier_payment_terms.{$this->value}");
    }
}
