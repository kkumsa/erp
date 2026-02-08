<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum MilestoneStatus: string implements HasLabel
{
    case Pending = 'pending';
    case InProgress = 'in_progress';
    case Completed = 'completed';
    case Delayed = 'delayed';

    public function getLabel(): string
    {
        return __("enums.milestone_status.{$this->value}");
    }

    public function color(): string
    {
        return match ($this) {
            self::Pending => 'gray',
            self::InProgress => 'info',
            self::Completed => 'success',
            self::Delayed => 'danger',
        };
    }
}
