<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum TaskStatus: string implements HasLabel
{
    case Pending = 'pending';
    case InProgress = 'in_progress';
    case InReview = 'in_review';
    case Completed = 'completed';
    case OnHold = 'on_hold';

    public function getLabel(): string
    {
        return __("enums.task_status.{$this->value}");
    }

    public function color(): string
    {
        return match ($this) {
            self::Pending => 'gray',
            self::InProgress => 'info',
            self::InReview => 'warning',
            self::Completed => 'success',
            self::OnHold => 'gray',
        };
    }
}
