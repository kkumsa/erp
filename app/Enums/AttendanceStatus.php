<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum AttendanceStatus: string implements HasLabel
{
    case Normal = 'normal';
    case Late = 'late';
    case EarlyLeave = 'early_leave';
    case Absent = 'absent';
    case OnLeave = 'on_leave';
    case BusinessTrip = 'business_trip';
    case Remote = 'remote';

    public function getLabel(): string
    {
        return __("enums.attendance_status.{$this->value}");
    }

    public function color(): string
    {
        return match ($this) {
            self::Normal => 'success',
            self::Late => 'warning',
            self::EarlyLeave => 'warning',
            self::Absent => 'danger',
            self::OnLeave => 'info',
            self::BusinessTrip => 'info',
            self::Remote => 'info',
        };
    }
}
