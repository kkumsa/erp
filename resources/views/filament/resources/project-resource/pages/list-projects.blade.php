<x-filament-panels::page>
    {{-- 테이블 --}}
    {{ $this->table }}

    {{-- 슬라이드 오버 패널 --}}
    <div
        wire:ignore.self
        x-data="{
            isShown: false,
            isLoading: false,
            pw: parseInt(localStorage.getItem('projectPanelWidth')) || 55,
            isResizing: false,
            minWidth: 35,
            maxWidth: 75,
            
            init() {
                this.pw = parseInt(localStorage.getItem('projectPanelWidth')) || 55;
                if (@js($slideOverMode && $selectedProjectId)) {
                    this.$nextTick(() => { this.isShown = true; });
                }
                if (typeof Livewire !== 'undefined') {
                    Livewire.hook('morph.updated', () => {
                        setTimeout(() => { this.isLoading = false; }, 150);
                    });
                }
            },
            
            close() {
                this.isShown = false;
                setTimeout(() => {
                    $wire.closePanel();
                }, 300);
            },
            
            startResize(e) {
                e.preventDefault();
                e.stopPropagation();
                this.isResizing = true;
                
                const self = this;
                
                function onMove(evt) {
                    if (!self.isResizing) return;
                    evt.preventDefault();
                    const vw = window.innerWidth;
                    let newWidth = ((vw - evt.clientX) / vw) * 100;
                    self.pw = Math.max(self.minWidth, Math.min(self.maxWidth, newWidth));
                    localStorage.setItem('projectPanelWidth', Math.round(self.pw));
                }
                
                function onUp(evt) {
                    self.isResizing = false;
                    document.removeEventListener('mousemove', onMove);
                    document.removeEventListener('mouseup', onUp);
                    document.body.style.cursor = '';
                    document.body.style.userSelect = '';
                    document.body.style.pointerEvents = '';
                }
                
                document.addEventListener('mousemove', onMove);
                document.addEventListener('mouseup', onUp);
                document.body.style.cursor = 'col-resize';
                document.body.style.userSelect = 'none';
                document.body.style.pointerEvents = 'none';
            }
        }"
        x-on:project-selected.window="pw = parseInt(localStorage.getItem('projectPanelWidth')) || 55; isShown = true; isLoading = true;"
        class="fixed inset-0 z-40 pointer-events-none overflow-hidden"
        x-show="isShown"
        style="display: none;"
    >
        <div
            x-show="isShown"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="translate-x-full"
            x-transition:enter-end="translate-x-0"
            x-transition:leave="transition ease-in duration-300"
            x-transition:leave-start="translate-x-0"
            x-transition:leave-end="translate-x-full"
            class="pointer-events-auto absolute top-0 bottom-0 flex bg-white dark:bg-gray-900 shadow-xl border-l border-gray-200 dark:border-gray-700"
            :style="'width: ' + pw + 'vw; right: 0;'"
        >
            {{-- 리사이저 --}}
            <div
                @mousedown.prevent.stop="startResize($event)"
                class="absolute left-0 top-0 bottom-0 w-3 cursor-col-resize z-10 group bg-white dark:bg-gray-900"
                style="margin-left: -12px;"
            >
                <div 
                    class="absolute right-0 top-0 bottom-0 w-px bg-gray-200 dark:bg-gray-700 group-hover:bg-primary-500 transition-colors"
                    :class="{ '!bg-primary-500': isResizing }"
                ></div>
            </div>

            {{-- 패널 콘텐츠 --}}
            <div class="flex-1 flex flex-col overflow-hidden">
                {{-- 헤더 --}}
                <div class="flex items-center gap-x-3 px-4 py-3 sticky top-0 z-10 border-b border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-900">
                    <div class="flex-1 min-w-0">
                        <h2 class="text-sm font-medium text-gray-950 dark:text-white truncate flex items-center gap-2">
                            <span x-show="isLoading" class="inline-flex items-center">
                                <svg class="animate-spin h-4 w-4 text-primary-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                            </span>
                            {{ $selectedProject?->name ?? '프로젝트 상세' }}
                        </h2>
                    </div>
                    <div class="flex items-center gap-x-2">
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
                        <button 
                            @click="close()"
                            type="button"
                            class="fi-icon-btn fi-modal-close-btn relative flex items-center justify-center rounded-lg outline-none transition duration-75 focus-visible:ring-2 h-9 w-9 text-gray-400 hover:text-gray-500 dark:text-gray-500 dark:hover:text-gray-400"
                        >
                            <x-heroicon-o-x-mark class="h-5 w-5" />
                        </button>
                    </div>
                </div>
                
                {{-- 콘텐츠 --}}
                <div class="flex-1 overflow-y-auto px-6 py-6">
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
                </div>
            </div>
        </div>
    </div>
</x-filament-panels::page>
