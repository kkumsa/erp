{{-- 인라인 버튼 방식: 모바일 터치에서 드롭다운이 즉시 닫히는 문제 방지 --}}
<div class="fi-locale-switcher flex items-center gap-0.5 rounded-lg border border-gray-200 bg-white p-0.5 dark:border-white/10 dark:bg-white/5">
    <button
        type="button"
        wire:click="switchLocale('ko')"
        class="min-w-[2.25rem] rounded-md px-2 py-1.5 text-center text-sm font-bold transition focus-visible:ring-2 focus-visible:ring-primary-500 {{ $locale === 'ko' ? 'bg-primary-500 text-white dark:bg-primary-500 dark:text-white' : 'text-gray-500 hover:bg-gray-100 hover:text-gray-700 dark:text-gray-400 dark:hover:bg-white/10 dark:hover:text-gray-200' }}"
        title="한국어"
    >
        KO
    </button>
    <button
        type="button"
        wire:click="switchLocale('en')"
        class="min-w-[2.25rem] rounded-md px-2 py-1.5 text-center text-sm font-bold transition focus-visible:ring-2 focus-visible:ring-primary-500 {{ $locale === 'en' ? 'bg-primary-500 text-white dark:bg-primary-500 dark:text-white' : 'text-gray-500 hover:bg-gray-100 hover:text-gray-700 dark:text-gray-400 dark:hover:bg-white/10 dark:hover:text-gray-200' }}"
        title="English"
    >
        EN
    </button>
</div>
