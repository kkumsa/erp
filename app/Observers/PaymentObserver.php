<?php

namespace App\Observers;

use App\Models\Payment;
use App\Models\Invoice;
use App\Models\User;
use App\Notifications\PaymentReceivedNotification;

class PaymentObserver
{
    public function created(Payment $payment): void
    {
        // 결제(입금) 생성 시 송장 생성자에게 알림
        $payable = $payment->payable;

        if ($payable instanceof Invoice && $payable->created_by) {
            $creator = User::find($payable->created_by);
            if ($creator) {
                // 결제 등록자 본인에게는 보내지 않음
                if ($creator->id !== auth()->id() && $creator->wantsNotification('payment_received')) {
                    $creator->notify(new PaymentReceivedNotification($payment));
                }
            }
        }
    }
}
