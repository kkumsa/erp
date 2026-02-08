<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum ProjectStatus: string implements HasLabel
{
    case Planning = 'planning';
    case InProgress = 'in_progress';
    case OnHold = 'on_hold';
    case Completed = 'completed';
    case Cancelled = 'cancelled';

    public function getLabel(): string
    {
        return __("enums.project_status.{$this->value}");
    }

    public function color(): string
    {
        return match ($this) {
            self::Planning => 'info',
            self::InProgress => 'success',
            self::OnHold => 'warning',
            self::Completed => 'success',
            self::Cancelled => 'danger',
        };
    }
}
