<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum PurchaseOrderStatus: string implements HasLabel
{
    case Draft = 'draft';
    case PendingApproval = 'pending_approval';
    case ApprovalRequested = 'approval_requested';
    case Approved = 'approved';
    case Ordered = 'ordered';
    case PartiallyReceived = 'partially_received';
    case Received = 'received';
    case Completed = 'completed';
    case Cancelled = 'cancelled';

    public function getLabel(): string
    {
        return __("enums.purchase_order_status.{$this->value}");
    }

    public function color(): string
    {
        return match ($this) {
            self::Draft => 'gray',
            self::PendingApproval => 'warning',
            self::ApprovalRequested => 'warning',
            self::Approved => 'info',
            self::Ordered => 'primary',
            self::PartiallyReceived => 'warning',
            self::Received => 'success',
            self::Completed => 'success',
            self::Cancelled => 'danger',
        };
    }
}
