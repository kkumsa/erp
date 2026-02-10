@php
    $user = filament()->auth()->user();
    $employee = $user->employee;
    $department = $employee?->department;

    // 부서 경로: "하드웨어 본부 > 설계팀" 형태로
    $departmentPath = '';
    if ($department) {
        $parts = collect();
        $current = $department;
        while ($current) {
            $parts->prepend($current->name);
            $current = $current->parent;
        }
        $departmentPath = $parts->join(' > ');
    }
@endphp

<x-filament-widgets::widget class="fi-account-widget h-full">
    <x-filament::section class="h-full">
        <div class="flex items-center gap-x-3">
            <x-filament-panels::avatar.user size="lg" :user="$user" />

            <div class="flex-1">
                @if($departmentPath)
                    <p class="text-xs text-gray-500 dark:text-gray-400">
                        {{ $departmentPath }}
                    </p>
                @endif

                <h2 class="text-base font-semibold leading-6 text-gray-950 dark:text-white">
                    {{ filament()->getUserName($user) }}
                </h2>

                @if($employee?->position)
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        {{ $employee->position }}
                    </p>
                @endif
            </div>

            <form
                action="{{ filament()->getLogoutUrl() }}"
                method="post"
                class="my-auto"
            >
                @csrf

                <x-filament::button
                    color="gray"
                    icon="heroicon-m-arrow-left-on-rectangle"
                    icon-alias="panels::widgets.account.logout-button"
                    labeled-from="sm"
                    tag="button"
                    type="submit"
                >
                    {{ __('filament-panels::widgets/account-widget.actions.logout.label') }}
                </x-filament::button>
            </form>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
