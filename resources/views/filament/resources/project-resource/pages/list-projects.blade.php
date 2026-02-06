<x-filament-panels::page>
    {{-- 테이블 --}}
    {{ $this->table }}

    <x-slide-over-panel
        storage-key="projectPanelWidth"
        :show="$slideOverMode && $selectedProjectId"
        open-event="project-selected"
    >
        <x-slot name="title">
            {{ $selectedProject?->name ?? '프로젝트 상세' }}
        </x-slot>

        <x-slot name="headerActions">
            @if($selectedProjectId)
                <a
                    href="{{ \App\Filament\Resources\ProjectResource::getUrl('view', ['record' => $selectedProjectId]) }}"
                    class="fi-icon-btn relative flex items-center justify-center rounded-lg outline-none transition duration-75 focus-visible:ring-2 h-9 w-9 text-gray-400 hover:text-gray-500 dark:text-gray-500 dark:hover:text-gray-400"
                    title="전체 화면으로 보기"
                >
                    <x-heroicon-o-arrows-pointing-out class="h-5 w-5" />
                </a>
                <a
                    href="{{ \App\Filament\Resources\ProjectResource::getUrl('edit', ['record' => $selectedProjectId]) }}"
                    class="fi-btn inline-grid grid-flow-col items-center justify-center gap-1.5 rounded-lg px-3 py-1.5 text-sm font-semibold shadow-sm bg-primary-600 text-white hover:bg-primary-500"
                >
                    <x-heroicon-m-pencil-square class="h-4 w-4" />
                    수정
                </a>
            @endif
        </x-slot>

        @if($selectedProjectId)
            {{ $this->projectInfolist }}

            <div class="mt-6">
                @livewire(\App\Filament\Resources\ProjectResource\RelationManagers\TasksRelationManager::class, [
                    'ownerRecord' => $selectedProject,
                    'pageClass' => \App\Filament\Resources\ProjectResource\Pages\ViewProject::class,
                ], key('tasks-' . $selectedProjectId))
            </div>

            <div class="mt-6">
                @livewire(\App\Filament\Resources\ProjectResource\RelationManagers\TimesheetsRelationManager::class, [
                    'ownerRecord' => $selectedProject,
                    'pageClass' => \App\Filament\Resources\ProjectResource\Pages\ViewProject::class,
                ], key('timesheets-' . $selectedProjectId))
            </div>
        @endif
    </x-slide-over-panel>
</x-filament-panels::page>
