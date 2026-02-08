<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum InvoiceStatus: string implements HasLabel
{
    case Draft = 'draft';
    case Issued = 'issued';
    case PartiallyPaid = 'partially_paid';
    case Paid = 'paid';
    case Overdue = 'overdue';
    case Cancelled = 'cancelled';

    public function getLabel(): string
    {
        return __("enums.invoice_status.{$this->value}");
    }

    public function color(): string
    {
        return match ($this) {
            self::Draft => 'gray',
            self::Issued => 'info',
            self::PartiallyPaid => 'warning',
            self::Paid => 'success',
            self::Overdue => 'danger',
            self::Cancelled => 'gray',
        };
    }
}
