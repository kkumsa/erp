<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum LeadStatus: string implements HasLabel
{
    case New = 'new';
    case Contacting = 'contacting';
    case Qualified = 'qualified';
    case Unqualified = 'unqualified';
    case Converted = 'converted';

    public function getLabel(): string
    {
        return __("enums.lead_status.{$this->value}");
    }

    public function color(): string
    {
        return match ($this) {
            self::New => 'info',
            self::Contacting => 'warning',
            self::Qualified => 'success',
            self::Unqualified => 'danger',
            self::Converted => 'success',
        };
    }
}
