<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum ContractStatus: string implements HasLabel
{
    case Drafting = 'drafting';
    case InReview = 'in_review';
    case PendingSignature = 'pending_signature';
    case Active = 'active';
    case Completed = 'completed';
    case Terminated = 'terminated';

    public function getLabel(): string
    {
        return __("enums.contract_status.{$this->value}");
    }

    public function color(): string
    {
        return match ($this) {
            self::Drafting => 'gray',
            self::InReview => 'warning',
            self::PendingSignature => 'info',
            self::Active => 'success',
            self::Completed => 'success',
            self::Terminated => 'danger',
        };
    }
}
