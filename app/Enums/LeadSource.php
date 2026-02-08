<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum LeadSource: string implements HasLabel
{
    case Website = 'website';
    case Referral = 'referral';
    case Advertisement = 'advertisement';
    case Exhibition = 'exhibition';
    case Other = 'other';

    public function getLabel(): string
    {
        return __("enums.lead_source.{$this->value}");
    }
}
