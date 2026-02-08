<div class="flex items-center">
    <x-filament::dropdown placement="bottom-end">
        <x-slot name="trigger">
            <button
                type="button"
                class="fi-icon-btn relative flex items-center justify-center rounded-lg outline-none transition duration-75 focus-visible:ring-2 h-9 w-9 text-gray-400 hover:text-gray-500 dark:text-gray-500 dark:hover:text-gray-400"
                title="{{ $locale === 'ko' ? '한국어' : 'English' }}"
            >
                <span class="text-sm font-bold">{{ $locale === 'ko' ? 'KO' : 'EN' }}</span>
            </button>
        </x-slot>

        <x-filament::dropdown.list>
            <x-filament::dropdown.list.item
                wire:click="switchLocale('ko')"
                :icon="$locale === 'ko' ? 'heroicon-s-check' : null"
            >
                한국어
            </x-filament::dropdown.list.item>

            <x-filament::dropdown.list.item
                wire:click="switchLocale('en')"
                :icon="$locale === 'en' ? 'heroicon-s-check' : null"
            >
                English
            </x-filament::dropdown.list.item>
        </x-filament::dropdown.list>
    </x-filament::dropdown>
</div>
