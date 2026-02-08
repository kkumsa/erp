<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum ApprovalStatus: string implements HasLabel
{
    case InProgress = 'in_progress';
    case Approved = 'approved';
    case Rejected = 'rejected';
    case Cancelled = 'cancelled';

    public function getLabel(): string
    {
        return __("enums.approval_status.{$this->value}");
    }

    public function color(): string
    {
        return match ($this) {
            self::InProgress => 'warning',
            self::Approved => 'success',
            self::Rejected => 'danger',
            self::Cancelled => 'gray',
        };
    }
}
