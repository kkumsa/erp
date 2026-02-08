<x-filament-panels::page>
    <div
        x-data="{
            draggingId: null,
            draggingName: '',
            draggingAmount: '',
            dropTargetId: null,
            showConfirm: false,
            targetInvoice: '',
            targetInvoiceId: null,

            startDrag(e, id, name, amount) {
                this.draggingId = id;
                this.draggingName = name;
                this.draggingAmount = amount;
                e.dataTransfer.effectAllowed = 'move';
                e.dataTransfer.setData('text/plain', id);
            },
            endDrag() {
                // 모달이 열려있으면 draggingId를 유지 (confirmMatch에서 사용)
                if (!this.showConfirm) {
                    this.draggingId = null;
                }
                this.dropTargetId = null;
            },
            dragOver(e, invoiceId) {
                e.preventDefault();
                e.dataTransfer.dropEffect = 'move';
                this.dropTargetId = invoiceId;
            },
            dragLeave() {
                this.dropTargetId = null;
            },
            drop(e, invoiceId, invoiceNumber) {
                e.preventDefault();
                this.dropTargetId = null;
                this.targetInvoiceId = invoiceId;
                this.targetInvoice = invoiceNumber;
                this.showConfirm = true;
            },
            confirmMatch() {
                $wire.matchDeposit(this.draggingId, this.targetInvoiceId);
                this.showConfirm = false;
                this.draggingId = null;
            },
            cancelMatch() {
                this.showConfirm = false;
                this.draggingId = null;
            }
        }"
        class="space-y-4"
    >
        {{-- 좌우 분할 레이아웃 --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">

            {{-- ===== 좌측: 청구서 목록 ===== --}}
            <div class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 flex flex-col" style="max-height: calc(100vh - 200px);">
                <div class="fi-section-header flex items-center gap-x-3 px-4 py-3 border-b border-gray-200 dark:border-white/10 flex-shrink-0">
                    <div class="flex-1">
                        <h3 class="text-sm font-semibold text-gray-950 dark:text-white flex items-center gap-2">
                            <x-heroicon-o-document-text class="h-4 w-4 text-gray-400" />
                            {{ __('common.payment_matching.invoice_list') }}
                        </h3>
                    </div>
                    <div class="flex items-center gap-2">
                        <select
                            wire:model.live="invoiceStatus"
                            class="text-xs rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 py-1"
                        >
                            <option value="unpaid">{{ __('common.payment_matching.unpaid_partial') }}</option>
                            <option value="all">{{ __('common.search.all') }}</option>
                        </select>
                        <input
                            wire:model.live.debounce.300ms="invoiceSearch"
                            type="text"
                            placeholder="{{ __('common.search.placeholder') }}"
                            class="text-xs rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 py-1 w-32"
                        />
                    </div>
                </div>

                <div class="overflow-auto flex-1">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 dark:bg-gray-800 sticky top-0 z-10">
                            <tr>
                                <th class="px-3 py-2 text-start text-xs font-medium text-gray-500 dark:text-gray-400 whitespace-nowrap">{{ __('common.payment_matching.invoice_number_col') }}</th>
                                <th class="px-3 py-2 text-start text-xs font-medium text-gray-500 dark:text-gray-400 whitespace-nowrap">{{ __('fields.customer') }}</th>
                                <th class="px-3 py-2 text-end text-xs font-medium text-gray-500 dark:text-gray-400 whitespace-nowrap">{{ __('common.payment_matching.invoice_amount') }}</th>
                                <th class="px-3 py-2 text-end text-xs font-medium text-gray-500 dark:text-gray-400 whitespace-nowrap">{{ __('fields.balance') }}</th>
                                <th class="px-3 py-2 text-center text-xs font-medium text-gray-500 dark:text-gray-400 whitespace-nowrap">{{ __('fields.status') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-white/5">
                            @foreach($this->getInvoices() as $invoice)
                                @php
                                    $balance = $invoice->total_amount - $invoice->paid_amount;
                                    $statusEnum = \App\Enums\InvoiceStatus::tryFrom($invoice->status);
                                @endphp
                                <tr
                                    x-on:dragover="dragOver($event, {{ $invoice->id }})"
                                    x-on:dragleave="dragLeave()"
                                    x-on:drop="drop($event, {{ $invoice->id }}, '{{ $invoice->invoice_number }}')"
                                    :class="{
                                        'bg-primary-50 dark:bg-primary-500/10 ring-2 ring-inset ring-primary-500': dropTargetId === {{ $invoice->id }},
                                        'hover:bg-gray-50 dark:hover:bg-white/5': dropTargetId !== {{ $invoice->id }}
                                    }"
                                    class="transition-colors cursor-default"
                                >
                                    <td class="px-3 py-2 font-medium text-gray-950 dark:text-white whitespace-nowrap">
                                        {{ $invoice->invoice_number }}
                                    </td>
                                    <td class="px-3 py-2 text-gray-600 dark:text-gray-400 whitespace-nowrap truncate max-w-[120px]">
                                        {{ $invoice->customer?->company_name ?? '-' }}
                                    </td>
                                    <td class="px-3 py-2 text-end text-gray-600 dark:text-gray-400 whitespace-nowrap">
                                        ₩{{ number_format($invoice->total_amount) }}
                                    </td>
                                    <td class="px-3 py-2 text-end font-semibold whitespace-nowrap {{ $balance > 0 ? 'text-danger-600 dark:text-danger-400' : 'text-success-600 dark:text-success-400' }}">
                                        ₩{{ number_format($balance) }}
                                    </td>
                                    <td class="px-3 py-2 text-center">
                                        @php
                                            $statusColor = $statusEnum?->color() ?? 'gray';
                                        @endphp
                                        <span class="inline-flex items-center rounded-md px-2 py-0.5 text-xs font-medium bg-{{ $statusColor }}-100 text-{{ $statusColor }}-700 dark:bg-{{ $statusColor }}-500/10 dark:text-{{ $statusColor }}-400">
                                            {{ $statusEnum?->getLabel() ?? $invoice->status }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    @if($this->getInvoices()->isEmpty())
                        <div class="px-4 py-8 text-center text-sm text-gray-500 dark:text-gray-400">
                            {{ __('common.payment_matching.no_invoices') }}
                        </div>
                    @endif
                </div>
            </div>

            {{-- ===== 우측: 입금 내역 ===== --}}
            <div class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 flex flex-col" style="max-height: calc(100vh - 200px);">
                <div class="fi-section-header flex items-center gap-x-3 px-4 py-3 border-b border-gray-200 dark:border-white/10 flex-shrink-0">
                    <div class="flex-1">
                        <h3 class="text-sm font-semibold text-gray-950 dark:text-white flex items-center gap-2">
                            <x-heroicon-o-building-library class="h-4 w-4 text-gray-400" />
                            {{ __('common.payment_matching.deposit_list') }}
                        </h3>
                    </div>
                    <div class="flex items-center gap-2">
                        <select
                            wire:model.live="depositStatus"
                            class="text-xs rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 py-1"
                        >
                            <option value="unprocessed">{{ __('common.payment_matching.unprocessed') }}</option>
                            <option value="processed">{{ __('common.payment_matching.processed') }}</option>
                            <option value="all">{{ __('common.search.all') }}</option>
                        </select>
                        <input
                            wire:model.live.debounce.300ms="depositSearch"
                            type="text"
                            placeholder="{{ __('common.search.placeholder') }}"
                            class="text-xs rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 py-1 w-32"
                        />
                    </div>
                </div>

                <div class="overflow-auto flex-1">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 dark:bg-gray-800 sticky top-0 z-10">
                            <tr>
                                <th class="px-3 py-2 text-center text-xs font-medium text-gray-500 dark:text-gray-400 whitespace-nowrap w-10">{{ __('common.payment_matching.processing_col') }}</th>
                                <th class="px-3 py-2 text-start text-xs font-medium text-gray-500 dark:text-gray-400 whitespace-nowrap">{{ __('common.payment_matching.deposit_date') }}</th>
                                <th class="px-3 py-2 text-start text-xs font-medium text-gray-500 dark:text-gray-400 whitespace-nowrap">{{ __('common.payment_matching.depositor') }}</th>
                                <th class="px-3 py-2 text-end text-xs font-medium text-gray-500 dark:text-gray-400 whitespace-nowrap">{{ __('fields.amount') }}</th>
                                <th class="px-3 py-2 text-center text-xs font-medium text-gray-500 dark:text-gray-400 whitespace-nowrap w-10">{{ __('common.table.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-white/5">
                            @foreach($this->getDeposits() as $deposit)
                                <tr
                                    @if(!$deposit->processed_at)
                                        draggable="true"
                                        x-on:dragstart="startDrag($event, {{ $deposit->id }}, '{{ addslashes($deposit->depositor_name) }}', '₩{{ number_format($deposit->amount) }}')"
                                        x-on:dragend="endDrag()"
                                        class="cursor-grab active:cursor-grabbing hover:bg-gray-50 dark:hover:bg-white/5 transition-colors"
                                    @else
                                        class="opacity-60"
                                    @endif
                                    :class="{ 'ring-2 ring-primary-500 bg-primary-50 dark:bg-primary-500/10': draggingId === {{ $deposit->id }} }"
                                >
                                    <td class="px-3 py-2 text-center">
                                        @if($deposit->processed_at)
                                            <x-heroicon-s-check-circle class="h-5 w-5 text-success-500 inline" />
                                        @else
                                            <div class="h-5 w-5 rounded border-2 border-gray-300 dark:border-gray-600 inline-block"></div>
                                        @endif
                                    </td>
                                    <td class="px-3 py-2 text-gray-600 dark:text-gray-400 whitespace-nowrap text-xs">
                                        {{ $deposit->deposited_at->format('m-d, H:i') }}
                                    </td>
                                    <td class="px-3 py-2 font-medium text-gray-950 dark:text-white whitespace-nowrap">
                                        {{ $deposit->depositor_name }}
                                    </td>
                                    <td class="px-3 py-2 text-end font-semibold whitespace-nowrap {{ $deposit->processed_at ? 'text-gray-400' : 'text-gray-950 dark:text-white' }}">
                                        ₩{{ number_format($deposit->amount) }}
                                    </td>
                                    <td class="px-3 py-2 text-center">
                                        @if($deposit->processed_at)
                                            <button
                                                wire:click="unmatchDeposit({{ $deposit->id }})"
                                                wire:confirm="{{ __('common.payment_matching.unmatch_confirm') }}"
                                                class="text-xs text-danger-600 hover:text-danger-800 dark:text-danger-400 dark:hover:text-danger-300"
                                                title="{{ __('common.buttons.unmatch') }}"
                                            >
                                                <x-heroicon-o-x-mark class="h-4 w-4 inline" />
                                            </button>
                                        @else
                                            <span class="text-xs text-gray-400">
                                                <x-heroicon-o-arrows-right-left class="h-4 w-4 inline" title="{{ __('common.payment_matching.drag_hint') }}" />
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    @if($this->getDeposits()->isEmpty())
                        <div class="px-4 py-8 text-center text-sm text-gray-500 dark:text-gray-400">
                            {{ __('common.payment_matching.no_deposits') }}
                        </div>
                    @endif
                </div>
            </div>

        </div>

        {{-- ===== 매칭 확인 모달 ===== --}}
        <div
            x-show="showConfirm"
            x-cloak
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/50"
            @keydown.escape.window="cancelMatch()"
        >
            <div
                x-show="showConfirm"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-95"
                class="bg-white dark:bg-gray-900 rounded-xl shadow-xl ring-1 ring-gray-950/5 dark:ring-white/10 p-6 max-w-md w-full mx-4"
                @click.away="cancelMatch()"
            >
                <div class="flex items-center gap-3 mb-4">
                    <div class="flex-shrink-0 w-10 h-10 rounded-full bg-primary-100 dark:bg-primary-500/10 flex items-center justify-center">
                        <x-heroicon-o-arrows-right-left class="h-5 w-5 text-primary-600 dark:text-primary-400" />
                    </div>
                    <div>
                        <h3 class="text-base font-semibold text-gray-950 dark:text-white">{{ __('common.payment_matching.confirm_title') }}</h3>
                    </div>
                </div>

                <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-4 mb-4 space-y-2 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-500">{{ __('common.payment_matching.confirm_depositor') }}</span>
                        <span class="font-medium text-gray-950 dark:text-white" x-text="draggingName"></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">{{ __('common.payment_matching.confirm_amount') }}</span>
                        <span class="font-semibold text-primary-600 dark:text-primary-400" x-text="draggingAmount"></span>
                    </div>
                    <div class="border-t border-gray-200 dark:border-gray-700 pt-2 flex justify-between">
                        <span class="text-gray-500">{{ __('common.payment_matching.confirm_invoice') }}</span>
                        <span class="font-medium text-gray-950 dark:text-white" x-text="targetInvoice"></span>
                    </div>
                </div>

                <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                    {{ __('common.payment_matching.confirm_message') }}
                </p>

                <div class="flex justify-end gap-3">
                    <button
                        @click="cancelMatch()"
                        type="button"
                        class="inline-flex items-center rounded-lg px-4 py-2 text-sm font-medium text-gray-700 bg-white ring-1 ring-gray-300 hover:bg-gray-50 dark:bg-gray-800 dark:text-gray-300 dark:ring-gray-700 dark:hover:bg-gray-700"
                    >
                        {{ __('common.buttons.cancel') }}
                    </button>
                    <button
                        @click="confirmMatch()"
                        type="button"
                        class="inline-flex items-center rounded-lg px-4 py-2 text-sm font-semibold text-white bg-primary-600 hover:bg-primary-500 shadow-sm"
                    >
                        {{ __('common.payment_matching.register_payment') }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</x-filament-panels::page>
