<?php

namespace App\Console\Commands;

use App\Models\Contract;
use App\Models\Invoice;
use App\Models\Product;
use App\Models\Stock;
use App\Models\User;
use App\Notifications\ContractExpiringNotification;
use App\Notifications\InvoiceOverdueNotification;
use App\Notifications\LowStockAlertNotification;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Notification;

class SendScheduledNotifications extends Command
{
    protected $signature = 'notifications:send-scheduled';
    protected $description = '정기 알림 발송 (송장 연체, 계약 만료 임박, 재고 부족)';

    public function handle(): int
    {
        $this->checkOverdueInvoices();
        $this->checkExpiringContracts();
        $this->checkLowStock();

        $this->info('정기 알림 발송 완료');

        return self::SUCCESS;
    }

    /**
     * 송장 연체 체크: due_date가 지나고 미결 상태인 송장
     * 하루 1회만 알림 (같은 송장에 대해 오늘 이미 알림이 있으면 건너뜀)
     */
    private function checkOverdueInvoices(): void
    {
        $overdueInvoices = Invoice::query()
            ->whereIn('status', ['발행', '연체'])
            ->whereNotNull('due_date')
            ->where('due_date', '<', now()->startOfDay())
            ->whereNotNull('created_by')
            ->with(['customer', 'creator'])
            ->get();

        $count = 0;
        foreach ($overdueInvoices as $invoice) {
            $creator = $invoice->creator;
            if (!$creator) {
                continue;
            }

            // 오늘 이미 같은 송장에 대한 연체 알림이 있으면 건너뜀
            $alreadyNotified = $creator->notifications()
                ->where('type', InvoiceOverdueNotification::class)
                ->whereDate('created_at', today())
                ->whereJsonContains('data->body', $invoice->invoice_number)
                ->exists();

            if ($alreadyNotified) {
                continue;
            }

            if (!$creator->wantsNotification('invoice_overdue')) {
                continue;
            }

            $creator->notify(new InvoiceOverdueNotification($invoice));
            $count++;
        }

        $this->info("  송장 연체 알림: {$count}건");
    }

    /**
     * 계약 만료 임박 체크: end_date가 30일 이내인 활성 계약
     * 30일, 14일, 7일, 3일, 1일 전에만 알림
     */
    private function checkExpiringContracts(): void
    {
        $thresholds = [30, 14, 7, 3, 1];

        $contracts = Contract::query()
            ->where('status', '활성')
            ->whereNotNull('end_date')
            ->where('end_date', '>', now())
            ->where('end_date', '<=', now()->addDays(30))
            ->with('customer')
            ->get();

        $admins = User::role(['Admin', 'Super Admin'])->get();
        $count = 0;

        foreach ($contracts as $contract) {
            $daysRemaining = (int) now()->diffInDays($contract->end_date, false);

            if (!in_array($daysRemaining, $thresholds)) {
                continue;
            }

            foreach ($admins as $admin) {
                // 오늘 이미 같은 계약에 대한 알림이 있으면 건너뜀
                $alreadyNotified = $admin->notifications()
                    ->where('type', ContractExpiringNotification::class)
                    ->whereDate('created_at', today())
                    ->whereJsonContains('data->body', $contract->title)
                    ->exists();

                if ($alreadyNotified) {
                    continue;
                }

                if (!$admin->wantsNotification('contract_expiring')) {
                    continue;
                }

                $admin->notify(new ContractExpiringNotification($contract, $daysRemaining));
                $count++;
            }
        }

        $this->info("  계약 만료 알림: {$count}건");
    }

    /**
     * 재고 부족 체크: 각 상품의 전체 재고 합이 min_stock 미만
     * 주 1회(월요일) 또는 재고가 0이면 매일 알림
     */
    private function checkLowStock(): void
    {
        $products = Product::query()
            ->where('min_stock', '>', 0)
            ->with('stocks')
            ->get();

        $managers = User::role(['Manager', 'Admin', 'Super Admin'])->get();
        $count = 0;

        foreach ($products as $product) {
            $totalStock = $product->stocks->sum('quantity');

            if ($totalStock >= $product->min_stock) {
                continue;
            }

            // 재고 0이면 매일, 그 외는 월요일만
            $isZeroStock = $totalStock <= 0;
            if (!$isZeroStock && now()->dayOfWeek !== 1) {
                continue;
            }

            foreach ($managers as $manager) {
                // 오늘 이미 같은 상품에 대한 알림이 있으면 건너뜀
                $alreadyNotified = $manager->notifications()
                    ->where('type', LowStockAlertNotification::class)
                    ->whereDate('created_at', today())
                    ->whereJsonContains('data->body', $product->name)
                    ->exists();

                if ($alreadyNotified) {
                    continue;
                }

                if (!$manager->wantsNotification('low_stock')) {
                    continue;
                }

                $manager->notify(new LowStockAlertNotification($product, $totalStock, $product->min_stock));
                $count++;
            }
        }

        $this->info("  재고 부족 알림: {$count}건");
    }
}
