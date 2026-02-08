<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum CustomerType: string implements HasLabel
{
    case Prospect = 'prospect';
    case Customer = 'customer';
    case Vip = 'vip';
    case Dormant = 'dormant';

    public function getLabel(): string
    {
        return __("enums.customer_type.{$this->value}");
    }

    public function color(): string
    {
        return match ($this) {
            self::Prospect => 'gray',
            self::Customer => 'success',
            self::Vip => 'warning',
            self::Dormant => 'danger',
        };
    }
}
