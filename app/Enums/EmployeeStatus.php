<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum EmployeeStatus: string implements HasLabel
{
    case Active = 'active';
    case OnLeave = 'on_leave';
    case Resigned = 'resigned';

    public function getLabel(): string
    {
        return __("enums.employee_status.{$this->value}");
    }

    public function color(): string
    {
        return match ($this) {
            self::Active => 'success',
            self::OnLeave => 'warning',
            self::Resigned => 'gray',
        };
    }
}
