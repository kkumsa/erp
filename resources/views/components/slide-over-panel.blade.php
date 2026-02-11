@props([
    'storageKey' => 'panelWidth',
    'defaultWidth' => 55,
    'minWidth' => 35,
    'maxWidth' => 75,
    'openEvent' => 'panel-open',
    'closeEvent' => 'panel-close',
    'show' => false,
    'closeDelayMs' => 300,
])

<div
    wire:ignore.self
    x-data="{
        isShown: false,
        isLoading: false,
        pw: parseInt(localStorage.getItem(@js($storageKey))) || @js($defaultWidth),
        isResizing: false,
        minWidth: @js($minWidth),
        maxWidth: @js($maxWidth),

        init() {
            this.pw = parseInt(localStorage.getItem(@js($storageKey))) || @js($defaultWidth);
            // 모바일(768px 미만)에서는 슬라이드 패널을 열지 않음
            if (window.innerWidth < 768) return;
            if (@js($show)) {
                this.$nextTick(() => { this.isShown = true; });
            }
            if (typeof Livewire !== 'undefined') {
                Livewire.hook('morph.updated', () => {
                    setTimeout(() => { this.isLoading = false; }, 150);
                });
            }
            // 패널 밖 클릭 시 닫기 (데이터 행 클릭은 제외)
            const self = this;
            document.addEventListener('click', function(e) {
                if (!self.isShown) return;
                // 패널 내부 클릭은 무시
                if (e.target.closest('.fi-slide-over-panel')) return;
                // 테이블 래퍼 밖이면 바로 닫기
                if (!e.target.closest('.fi-list-table-wrapper')) { self.close(); return; }
                // 테이블 래퍼 안: 데이터 행(그룹 헤더 아닌)은 무시
                const tr = e.target.closest('tbody tr');
                if (tr && !tr.querySelector('.fi-ta-group-header')) return;
                // 그 외(헤더, 그룹 헤더, 빈 공간 등)는 닫기
                self.close();
            });
        },

        close() {
            this.isShown = false;
            setTimeout(() => {
                $wire.closePanel();
            }, @js($closeDelayMs));
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
                localStorage.setItem(@js($storageKey), Math.round(self.pw));
            }

            function onUp() {
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
    x-on:{{ $openEvent }}.window="if (window.innerWidth < 768) return; pw = parseInt(localStorage.getItem(@js($storageKey))) || @js($defaultWidth); isShown = true; isLoading = true;"
    x-on:{{ $closeEvent }}.window="if (isShown) { close(); }"
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
        class="fi-slide-over-panel pointer-events-auto absolute top-0 bottom-0 flex bg-white dark:bg-gray-900 shadow-xl border-l border-gray-200 dark:border-gray-700"
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
                        {{ $title ?? __('common.general.detail') }}
                    </h2>
                </div>
                <div class="flex items-center gap-x-2">
                    {{ $headerActions ?? '' }}
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
                {{ $slot }}
            </div>
        </div>
    </div>
</div>
