<x-filament-panels::page>
    <div
        x-data
        @click="
            if (!$wire.slideOverMode || !$wire.selectedRecordId) return;
            const target = $event.target;
            const tableRoot = target.closest('.fi-ta');
            if (!tableRoot) return;
            if (target.closest('tbody tr')) return;
            if (target.closest('button, a, input, select, textarea, label, [role=button]')) return;
            window.dispatchEvent(new CustomEvent('panel-close'));
        "
    >
        {{ $this->table }}
    </div>

    <x-slide-over-panel
        storage-key="supplierPanelWidth"
        :show="$slideOverMode && $selectedRecordId"
        open-event="record-selected"
    >
        <x-slot name="title">
            {{ $selectedRecord?->company_name ?? '공급업체 상세' }}
        </x-slot>

        <x-slot name="headerActions">
            @if($selectedRecordId)
                <a
                    href="{{ \App\Filament\Resources\SupplierResource::getUrl('edit', ['record' => $selectedRecordId]) }}"
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
