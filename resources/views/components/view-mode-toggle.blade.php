@props([
    'storageKey' => 'projectListViewMode',
    'wireMethod' => 'setSlideOverMode',
    'defaultMode' => 'slide',
])

<div
    x-data="{
        open: false,
        isMobile: window.innerWidth < 768,
        slideOverMode: @js($defaultMode === 'slide'),

        init() {
            this.isMobile = window.innerWidth < 768;
            this.slideOverMode = this.isMobile ? false : (localStorage.getItem(@js($storageKey)) !== 'page');

            window.addEventListener('resize', () => {
                const wasMobile = this.isMobile;
                this.isMobile = window.innerWidth < 768;
                if (this.isMobile && !wasMobile) {
                    this.slideOverMode = false;
                    if (typeof $wire !== 'undefined') {
                        $wire[@js($wireMethod)](false);
                    }
                }
            });

            if (typeof $wire !== 'undefined') {
                $wire[@js($wireMethod)](this.slideOverMode);
            }
        },

        select(mode) {
            this.slideOverMode = (mode === 'slide');
            localStorage.setItem(@js($storageKey), this.slideOverMode ? 'slideOver' : 'page');
            this.open = false;
            if (typeof $wire !== 'undefined') {
                $wire[@js($wireMethod)](this.slideOverMode);
            }
        }
    }"
    @click.away="open = false"
    x-show="!isMobile"
    class="relative hidden sm:block"
>
    <button
        @click="open = !open"
        type="button"
        class="relative flex items-center justify-center rounded-lg p-2 outline-none transition duration-75 hover:bg-gray-50 dark:hover:bg-white/5 text-primary-500 dark:text-primary-400"
    >
        {{-- 슬라이드 아이콘 --}}
        <svg x-show="slideOverMode" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 21L3 16.5m0 0L7.5 12M3 16.5h13.5m0-13.5L21 7.5m0 0L16.5 12M21 7.5H7.5" />
        </svg>
        {{-- 페이지 아이콘 --}}
        <svg x-show="!slideOverMode" x-cloak xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 003 8.25v10.5A2.25 2.25 0 005.25 21h10.5A2.25 2.25 0 0018 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25" />
        </svg>
    </button>

    {{-- 드롭다운 --}}
    <div
        x-show="open"
        x-transition:enter="transition ease-out duration-100"
        x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-75"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
        class="absolute mt-1 w-32 rounded-lg bg-white dark:bg-gray-900 shadow-lg ring-1 ring-gray-950/5 dark:ring-white/10 p-1 z-50"
        style="display: none; right: 100%; margin-right: -2rem;"
    >
        <button
            @click="select('page')"
            type="button"
            class="flex items-center gap-2 w-full px-3 py-2 text-sm rounded-md transition"
            :class="!slideOverMode ? 'bg-primary-50 text-primary-700 dark:bg-primary-500/10 dark:text-primary-400' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-white/5'"
        >
            <x-heroicon-o-arrow-top-right-on-square class="w-4 h-4" />
            {{ __('common.view_mode.page') }}
        </button>
        <button
            @click="select('slide')"
            type="button"
            class="flex items-center gap-2 w-full px-3 py-2 text-sm rounded-md transition"
            :class="slideOverMode ? 'bg-primary-50 text-primary-700 dark:bg-primary-500/10 dark:text-primary-400' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-white/5'"
        >
            <x-heroicon-o-arrows-right-left class="w-4 h-4" />
            {{ __('common.view_mode.slide') }}
        </button>
    </div>
</div>
