<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum OpportunityStage: string implements HasLabel
{
    case Discovery = 'discovery';
    case Contact = 'contact';
    case Proposal = 'proposal';
    case Negotiation = 'negotiation';
    case ClosedWon = 'closed_won';
    case ClosedLost = 'closed_lost';

    public function getLabel(): string
    {
        return __("enums.opportunity_stage.{$this->value}");
    }

    public function color(): string
    {
        return match ($this) {
            self::Discovery => 'gray',
            self::Contact => 'info',
            self::Proposal => 'warning',
            self::Negotiation => 'warning',
            self::ClosedWon => 'success',
            self::ClosedLost => 'danger',
        };
    }
}
