<x-filament-panels::page>
    {{-- 테이블 --}}
    {{ $this->table }}

    {{-- 뷰 모드 토글 버튼 (일괄 작업 버튼 옆에 주입) --}}
    <div
        wire:ignore
        x-data="{
            slideOverMode: localStorage.getItem('projectListViewMode') !== 'page',
            btn: null,

            init() {
                $wire.slideOverMode = this.slideOverMode;
                this.createButton();
                this.placeButton();

                // Livewire 업데이트 후 재배치
                const self = this;
                const observer = new MutationObserver(() => self.placeButton());
                observer.observe(document.body, { childList: true, subtree: true });
            },

            createButton() {
                this.btn = document.createElement('button');
                this.btn.type = 'button';
                this.btn.id = 'project-view-toggle';
                this.updateButton();
                
                const self = this;
                this.btn.addEventListener('click', function() {
                    self.slideOverMode = !self.slideOverMode;
                    localStorage.setItem('projectListViewMode', self.slideOverMode ? 'slideOver' : 'page');
                    self.updateButton();
                    $wire.setSlideOverMode(self.slideOverMode);
                });
            },

            updateButton() {
                if (!this.btn) return;
                this.btn.textContent = this.slideOverMode ? '슬라이드' : '전환';
                if (this.slideOverMode) {
                    this.btn.className = 'fi-btn fi-btn-size-sm inline-grid grid-flow-col items-center justify-center rounded-lg px-2.5 py-1 text-xs outline-none transition duration-75 border border-gray-200 bg-white text-gray-400 hover:text-gray-600 hover:border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-500 dark:hover:text-gray-300';
                } else {
                    this.btn.className = 'fi-btn fi-btn-size-sm inline-grid grid-flow-col items-center justify-center rounded-lg px-2.5 py-1 text-xs outline-none transition duration-75 border border-primary-300 bg-primary-50 text-primary-400 hover:bg-primary-100 hover:text-primary-500 dark:border-primary-700 dark:bg-primary-900/30 dark:text-primary-500 dark:hover:text-primary-400';
                }
            },

            placeButton() {
                if (!this.btn) return;
                // toolbar 좌측 div 찾기 (일괄 작업 버튼이 있는 곳)
                const toolbar = document.querySelector('.fi-ta-header-toolbar > div:first-child');
                if (toolbar && !toolbar.contains(this.btn)) {
                    toolbar.insertBefore(this.btn, toolbar.firstChild);
                }
            }
        }"
    ></div>

    {{-- 슬라이드 오버 패널 --}}
    @if($slideOverMode && $selectedProjectId)
        <div
            x-data="{
                isShown: false,
                panelWidth: parseInt(localStorage.getItem('projectPanelWidth')) || 55,
                isResizing: false,
                minWidth: 35,
                maxWidth: 75,
                
                init() {
                    this.$nextTick(() => { this.isShown = true; });
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
                        self.panelWidth = Math.max(self.minWidth, Math.min(self.maxWidth, newWidth));
                    }
                    
                    function onUp(evt) {
                        self.isResizing = false;
                        localStorage.setItem('projectPanelWidth', self.panelWidth);
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
            class="fixed inset-0 z-40 pointer-events-none overflow-hidden"
        >
            {{-- 패널 (오른쪽에 붙음) --}}
            <div
                x-show="isShown"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="translate-x-full"
                x-transition:enter-end="translate-x-0"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="translate-x-0"
                x-transition:leave-end="translate-x-full"
                class="pointer-events-auto absolute top-0 bottom-0 flex bg-white dark:bg-gray-900 shadow-xl border-l border-gray-200 dark:border-gray-700"
                :style="'width: ' + panelWidth + 'vw; right: 0;'"
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
                    <div class="flex items-center gap-x-5 px-4 py-3 sticky top-0 z-10 border-b border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-900">
                        <div class="flex-1 min-w-0">
                            <h2 class="text-sm font-medium text-gray-950 dark:text-white truncate">
                                {{ $selectedProject?->name ?? '프로젝트 상세' }}
                            </h2>
                        </div>
                        <div class="flex items-center gap-x-2">
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
                            <button 
                                wire:click="closePanel"
                                type="button"
                                class="fi-icon-btn fi-modal-close-btn relative flex items-center justify-center rounded-lg outline-none transition duration-75 focus-visible:ring-2 h-9 w-9 text-gray-400 hover:text-gray-500 dark:text-gray-500 dark:hover:text-gray-400"
                            >
                                <x-heroicon-o-x-mark class="h-5 w-5" />
                            </button>
                        </div>
                    </div>
                    
                    {{-- 콘텐츠 --}}
                    <div class="flex-1 overflow-y-auto px-6 py-6">
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
                    </div>
                </div>
            </div>
        </div>
    @endif
</x-filament-panels::page>
