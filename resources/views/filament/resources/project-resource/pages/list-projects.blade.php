<x-filament-panels::page>
    {{-- 테이블 --}}
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
        storage-key="projectPanelWidth"
        :show="$slideOverMode && $selectedRecordId"
        open-event="record-selected"
    >
        <x-slot name="title">
            {{ $selectedRecord?->name ?? '프로젝트 상세' }}
        </x-slot>

        <x-slot name="headerActions">
            @if($selectedRecordId)
                <a
                    href="{{ \App\Filament\Resources\ProjectResource::getUrl('view', ['record' => $selectedRecordId]) }}"
                    class="fi-icon-btn relative flex items-center justify-center rounded-lg outline-none transition duration-75 focus-visible:ring-2 h-9 w-9 text-gray-400 hover:text-gray-500 dark:text-gray-500 dark:hover:text-gray-400"
                    title="전체 화면으로 보기"
                >
                    <x-heroicon-o-arrows-pointing-out class="h-5 w-5" />
                </a>
                <a
                    href="{{ \App\Filament\Resources\ProjectResource::getUrl('edit', ['record' => $selectedRecordId]) }}"
                    class="fi-btn inline-grid grid-flow-col items-center justify-center gap-1.5 rounded-lg px-3 py-1.5 text-sm font-semibold shadow-sm bg-primary-600 text-white hover:bg-primary-500"
                >
                    <x-heroicon-m-pencil-square class="h-4 w-4" />
                    수정
                </a>
            @endif
        </x-slot>

        @if($selectedRecordId)
            {{ $this->recordInfolist }}

            <div class="mt-6">
                @livewire(\App\Filament\Resources\ProjectResource\RelationManagers\TasksRelationManager::class, [
                    'ownerRecord' => $selectedRecord,
                    'pageClass' => \App\Filament\Resources\ProjectResource\Pages\ViewProject::class,
                ], key('tasks-' . $selectedRecordId))
            </div>

            <div class="mt-6">
                @livewire(\App\Filament\Resources\ProjectResource\RelationManagers\TimesheetsRelationManager::class, [
                    'ownerRecord' => $selectedRecord,
                    'pageClass' => \App\Filament\Resources\ProjectResource\Pages\ViewProject::class,
                ], key('timesheets-' . $selectedRecordId))
            </div>
        @endif
    </x-slide-over-panel>
</x-filament-panels::page>
