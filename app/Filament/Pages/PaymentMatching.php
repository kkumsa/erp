<?php

namespace App\Filament\Pages;

use App\Enums\PaymentMethod;
use App\Models\BankDeposit;
use App\Models\Invoice;
use App\Models\Payment;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class PaymentMatching extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-arrows-right-left';

    protected static ?int $navigationSort = 1;

    protected static string $view = 'filament.pages.payment-matching';

    // 필터 상태
    public string $invoiceSearch = '';
    public string $invoiceStatus = 'unpaid'; // unpaid, all
    public string $depositSearch = '';
    public string $depositStatus = 'unprocessed'; // unprocessed, processed, all

    public static function getNavigationGroup(): ?string
    {
        return __('navigation.groups.finance');
    }

    public static function getNavigationLabel(): string
    {
        return __('navigation.labels.payment_matching');
    }

    public function getTitle(): string|\Illuminate\Contracts\Support\Htmlable
    {
        return __('common.pages.payment_matching');
    }

    /**
     * 청구서 목록 (미결제/부분결제)
     */
    public function getInvoices(): \Illuminate\Support\Collection
    {
        $query = Invoice::query()
            ->with(['customer', 'payments'])
            ->orderBy('due_date');

        if ($this->invoiceStatus === 'unpaid') {
            $query->whereIn('status', ['issued', 'partially_paid', 'overdue']);
        }

        if ($this->invoiceSearch) {
            $search = $this->invoiceSearch;
            $query->where(function ($q) use ($search) {
                $q->where('invoice_number', 'like', "%{$search}%")
                  ->orWhereHas('customer', fn ($cq) => $cq->where('company_name', 'like', "%{$search}%"));
            });
        }

        return $query->limit(50)->get();
    }

    /**
     * 입금 내역 목록
     */
    public function getDeposits(): \Illuminate\Support\Collection
    {
        $query = BankDeposit::query()
            ->with('payment')
            ->orderByDesc('deposited_at');

        if ($this->depositStatus === 'unprocessed') {
            $query->whereNull('processed_at');
        } elseif ($this->depositStatus === 'processed') {
            $query->whereNotNull('processed_at');
        }

        if ($this->depositSearch) {
            $search = $this->depositSearch;
            $query->where(function ($q) use ($search) {
                $q->where('depositor_name', 'like', "%{$search}%")
                  ->orWhere('transaction_number', 'like', "%{$search}%")
                  ->orWhere('amount', 'like', "%{$search}%");
            });
        }

        return $query->limit(100)->get();
    }

    /**
     * 드래그앤드롭 매칭: 입금 내역을 청구서에 결제로 등록
     */
    public function matchDeposit(int $depositId, int $invoiceId): void
    {
        $deposit = BankDeposit::find($depositId);
        $invoice = Invoice::find($invoiceId);

        if (!$deposit || !$invoice) {
            Notification::make()
                ->title(__('common.notifications.matching_failed'))
                ->body(__('common.notifications.matching_not_found'))
                ->danger()
                ->send();
            return;
        }

        if ($deposit->processed_at) {
            Notification::make()
                ->title(__('common.notifications.matching_already_processed'))
                ->body(__('common.notifications.matching_already_processed_body'))
                ->warning()
                ->send();
            return;
        }

        // Payment 레코드 생성
        $payment = Payment::create([
            'payable_type' => Invoice::class,
            'payable_id' => $invoice->id,
            'payment_date' => $deposit->deposited_at->toDateString(),
            'amount' => $deposit->amount,
            'method' => PaymentMethod::BankTransfer->value,
            'reference' => $deposit->transaction_number,
            'note' => __('common.payment_matching.deposit_matching_note', ['name' => $deposit->depositor_name]),
            'recorded_by' => auth()->id(),
        ]);

        // BankDeposit 처리 완료 표시
        $deposit->update([
            'processed_at' => now(),
            'payment_id' => $payment->id,
        ]);

        Notification::make()
            ->title(__('common.notifications.matching_success'))
            ->body(__('common.payment_matching.matching_success_body', [
                'name' => $deposit->depositor_name,
                'amount' => number_format($deposit->amount),
                'invoice' => $invoice->invoice_number,
            ]))
            ->success()
            ->send();
    }

    /**
     * 매칭 해제: 입금 내역과 청구서의 결제 연결을 해제
     */
    public function unmatchDeposit(int $depositId): void
    {
        $deposit = BankDeposit::find($depositId);

        if (!$deposit || !$deposit->processed_at) {
            Notification::make()
                ->title(__('common.notifications.unmatching_failed'))
                ->body(__('common.notifications.unmatching_not_found'))
                ->danger()
                ->send();
            return;
        }

        // 연결된 Payment 삭제
        if ($deposit->payment_id) {
            $payment = Payment::find($deposit->payment_id);
            if ($payment) {
                $payable = $payment->payable;
                $payment->forceDelete();

                // Invoice 상태 갱신
                if ($payable instanceof Invoice) {
                    $payable->updatePaymentStatus();
                }
            }
        }

        // BankDeposit 처리 해제
        $deposit->update([
            'processed_at' => null,
            'payment_id' => null,
        ]);

        Notification::make()
            ->title(__('common.notifications.unmatching_success'))
            ->body(__('common.payment_matching.unmatching_success_body', ['name' => $deposit->depositor_name]))
            ->info()
            ->send();
    }

    public static function canAccess(): bool
    {
        $user = auth()->user();
        return $user && ($user->hasAnyRole(['Super Admin', 'Admin', 'Accountant', 'Manager']) || $user->can('payment.view'));
    }

    public static function shouldRegisterNavigation(): bool
    {
        return static::canAccess();
    }
}
