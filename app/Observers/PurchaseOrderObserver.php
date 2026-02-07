<?php

namespace App\Observers;

use App\Models\PurchaseOrder;
use App\Models\User;
use App\Notifications\PurchaseOrderApprovalNotification;

class PurchaseOrderObserver
{
    public function created(PurchaseOrder $purchaseOrder): void
    {
        // 결재라인이 설정되어 있으면 결재라인 시스템으로 대체
        // (결재 요청은 EditPurchaseOrder 페이지에서 수동으로 시작)
        // 결재라인이 없는 경우에만 기존 방식으로 Manager/Admin에게 알림
        $flow = \App\Models\ApprovalFlow::findForTarget($purchaseOrder);
        if ($flow) {
            // 결재라인이 존재하므로 수동 승인요청을 기다림
            return;
        }

        // 결재라인이 없는 경우 기존 로직: Manager/Admin에게 단순 알림
        $managers = User::role(['Manager', 'Admin'])->get();
        foreach ($managers as $manager) {
            if ($manager->id === $purchaseOrder->created_by) {
                continue;
            }
            if ($manager->wantsNotification('purchase_order_approval')) {
                $manager->notify(new PurchaseOrderApprovalNotification($purchaseOrder));
            }
        }
    }
}
