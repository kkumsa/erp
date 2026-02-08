<?php

namespace App\Enums\Concerns;

use Filament\Support\Contracts\HasLabel as FilamentHasLabel;

/**
 * Enum에 Filament 호환 label() 메서드를 제공하는 trait.
 * 번역 키: enums.{snake_case_enum_name}.{value}
 */
trait HasLabel
{
    public function getLabel(): string
    {
        $enumName = class_basename(static::class);
        // PascalCase → snake_case
        $key = strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $enumName));

        return __("enums.{$key}.{$this->value}");
    }
}
