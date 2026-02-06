<x-filament-panels::page>
    {{ $this->table }}

    <x-slide-over-panel
        storage-key="employeePanelWidth"
        :show="$slideOverMode && $selectedRecordId"
        open-event="record-selected"
    >
        <x-slot name="title">
            {{ $selectedRecord?->user?->name ?? '직원 상세' }}
        </x-slot>

        <x-slot name="headerActions">
            @if($selectedRecordId)
                <a
                    href="{{ \App\Filament\Resources\EmployeeResource::getUrl('edit', ['record' => $selectedRecordId]) }}"
                    class="fi-btn inline-grid grid-flow-col items-center justify-center gap-1.5 rounded-lg px-3 py-1.5 text-sm font-semibold shadow-sm bg-primary-600 text-white hover:bg-primary-500"
                >
                    <x-heroicon-m-pencil-square class="h-4 w-4" />
                    수정
                </a>
            @endif
        </x-slot>

        @if($selectedRecordId)
            {{ $this->recordInfolist }}
        @endif
    </x-slide-over-panel>
</x-filament-panels::page>
