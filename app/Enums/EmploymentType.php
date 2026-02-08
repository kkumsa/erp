<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum EmploymentType: string implements HasLabel
{
    case FullTime = 'full_time';
    case Contract = 'contract';
    case Intern = 'intern';
    case PartTime = 'part_time';

    public function getLabel(): string
    {
        return __("enums.employment_type.{$this->value}");
    }

    public function color(): string
    {
        return match ($this) {
            self::FullTime => 'success',
            self::Contract => 'warning',
            self::Intern => 'info',
            self::PartTime => 'gray',
        };
    }
}
