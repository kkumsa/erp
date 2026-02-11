<x-filament-panels::page>
    <x-list-table-wrapper>
        {{ $this->table }}
    </x-list-table-wrapper>

    <x-slide-over-panel
        storage-key="bankDepositPanelWidth"
        :show="$slideOverMode && $selectedRecordId"
        open-event="record-selected"
    >
        <x-slot name="title">
            {{ $selectedRecord?->depositor_name ?? '입금 내역 상세' }}
            @if($selectedRecord)
                <span class="text-sm font-normal text-gray-500"> · ₩{{ number_format($selectedRecord->amount) }}</span>
            @endif
        </x-slot>

        <x-slot name="headerActions">
            @if($selectedRecordId)
                <a
                    href="{{ \App\Filament\Resources\BankDepositResource::getUrl('edit', ['record' => $selectedRecordId]) }}"
                    class="fi-btn inline-grid grid-flow-col items-center justify-center gap-1.5 rounded-lg px-3 py-1.5 text-sm font-semibold shadow-sm bg-primary-600 text-white hover:bg-primary-500"
                >
                    <x-heroicon-m-pencil-square class="h-4 w-4" />
                    {{ __('common.buttons.edit') }}
                </a>
            @endif
        </x-slot>

        @if($selectedRecordId)
            {{ $this->recordInfolist }}

            <div class="mt-6">
                <x-activity-log-panel
                    :subject-type="get_class($selectedRecord)"
                    :subject-id="$selectedRecordId"
                />
            </div>
        @endif
    </x-slide-over-panel>
</x-filament-panels::page>
