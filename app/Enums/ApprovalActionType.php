<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum ApprovalActionType: string implements HasLabel
{
    case Approval = 'approval';
    case Agreement = 'agreement';
    case Reference = 'reference';

    public function getLabel(): string
    {
        return __("enums.approval_action_type.{$this->value}");
    }
}
