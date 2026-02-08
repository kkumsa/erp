<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum ApprovalAction: string implements HasLabel
{
    case Approved = 'approved';
    case Rejected = 'rejected';
    case Acknowledged = 'acknowledged';
    case AutoSkipped = 'auto_skipped';

    public function getLabel(): string
    {
        return __("enums.approval_action.{$this->value}");
    }

    public function color(): string
    {
        return match ($this) {
            self::Approved => 'success',
            self::Rejected => 'danger',
            self::Acknowledged => 'info',
            self::AutoSkipped => 'gray',
        };
    }
}
