<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum StockMovementType: string implements HasLabel
{
    case Incoming = 'incoming';
    case Outgoing = 'outgoing';
    case Adjustment = 'adjustment';
    case Transfer = 'transfer';
    case ReturnStock = 'return_stock';

    public function getLabel(): string
    {
        return __("enums.stock_movement_type.{$this->value}");
    }

    public function color(): string
    {
        return match ($this) {
            self::Incoming => 'success',
            self::Outgoing => 'danger',
            self::Adjustment => 'warning',
            self::Transfer => 'info',
            self::ReturnStock => 'gray',
        };
    }
}
