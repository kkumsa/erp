@php
    $current = app()->getLocale();
@endphp
<div class="mb-6 flex items-center justify-center gap-2 py-2 text-sm text-gray-500 dark:text-gray-400">
    <a
        href="{{ route('locale.switch', 'ko') }}"
        class="rounded-md outline-none hover:underline focus:underline {{ $current === 'ko' ? 'font-semibold text-primary-600 dark:text-primary-400' : '' }}"
    >
        한국어
    </a>
    <span aria-hidden="true">|</span>
    <a
        href="{{ route('locale.switch', 'en') }}"
        class="rounded-md outline-none hover:underline focus:underline {{ $current === 'en' ? 'font-semibold text-primary-600 dark:text-primary-400' : '' }}"
    >
        English
    </a>
</div>
