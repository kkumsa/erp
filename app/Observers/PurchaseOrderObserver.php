<?php

namespace App\Observers;

use App\Models\PurchaseOrder;
use App\Models\User;
use App\Notifications\PurchaseOrderApprovalNotification;

class PurchaseOrderObserver
{
    public function created(PurchaseOrder $purchaseOrder): void
    {
        // 구매주문 생성 시 Manager/Admin에게 승인 요청 알림
        $managers = User::role(['Manager', 'Admin'])->get();
        foreach ($managers as $manager) {
            // 생성자 본인에게는 보내지 않음
            if ($manager->id === $purchaseOrder->created_by) {
                continue;
            }
            if ($manager->wantsNotification('purchase_order_approval')) {
                $manager->notify(new PurchaseOrderApprovalNotification($purchaseOrder));
            }
        }
    }
}
