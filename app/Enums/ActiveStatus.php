<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

/**
 * Customer, Supplier 등에서 공용으로 사용하는 활성/비활성 상태.
 */
enum ActiveStatus: string implements HasLabel
{
    case Active = 'active';
    case Inactive = 'inactive';

    public function getLabel(): string
    {
        return __("enums.active_status.{$this->value}");
    }

    public function color(): string
    {
        return match ($this) {
            self::Active => 'success',
            self::Inactive => 'gray',
        };
    }
}
