<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum TimesheetStatus: string implements HasLabel
{
    case Pending = 'pending';
    case ApprovalRequested = 'approval_requested';
    case Approved = 'approved';
    case Rejected = 'rejected';

    public function getLabel(): string
    {
        return __("enums.timesheet_status.{$this->value}");
    }

    public function color(): string
    {
        return match ($this) {
            self::Pending => 'gray',
            self::ApprovalRequested => 'warning',
            self::Approved => 'success',
            self::Rejected => 'danger',
        };
    }
}
