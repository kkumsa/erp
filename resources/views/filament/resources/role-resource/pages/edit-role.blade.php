<x-filament-panels::page>
    <div class="space-y-6">
        {{-- 상단 액션 버튼 --}}
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-2">
                <x-filament::button
                    color="gray"
                    size="sm"
                    wire:click="selectAll"
                >
                    전체 선택
                </x-filament::button>
                <x-filament::button
                    color="gray"
                    size="sm"
                    wire:click="deselectAll"
                >
                    전체 해제
                </x-filament::button>
            </div>
            <x-filament::button
                wire:click="save"
                icon="heroicon-o-check"
            >
                저장
            </x-filament::button>
        </div>

        {{-- 권한 매트릭스 테이블 --}}
        <div class="fi-ta rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full table-auto divide-y divide-gray-200 dark:divide-white/5">
                    <thead class="bg-gray-50 dark:bg-white/5">
                        <tr>
                            <th class="px-4 py-3 text-left text-sm font-medium text-gray-600 dark:text-gray-400 whitespace-nowrap">
                                모듈
                            </th>
                            @foreach($this->allActions as $action)
                                <th class="px-3 py-3 text-center text-sm font-medium text-gray-600 dark:text-gray-400 whitespace-nowrap">
                                    {{ $this->actionLabels[$action] ?? $action }}
                                </th>
                            @endforeach
                            <th class="px-3 py-3 text-center text-sm font-medium text-gray-600 dark:text-gray-400 whitespace-nowrap">
                                전체
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-white/5">
                        @foreach($this->moduleDefinitions as $module => $config)
                            <tr class="hover:bg-gray-50 dark:hover:bg-white/5 transition-colors">
                                <td class="px-4 py-2.5 text-sm font-medium text-gray-900 dark:text-white whitespace-nowrap">
                                    {{ $config['label'] }}
                                    <span class="text-xs text-gray-400 dark:text-gray-500 ml-1">({{ $module }})</span>
                                </td>
                                @foreach($this->allActions as $action)
                                    <td class="px-3 py-2.5 text-center">
                                        @if(in_array($action, $config['actions']))
                                            <label class="inline-flex items-center cursor-pointer">
                                                <input
                                                    type="checkbox"
                                                    wire:model.live="permissions.{{ $module }}.{{ $action }}"
                                                    class="fi-checkbox-input rounded border-gray-300 text-primary-600 shadow-sm focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-700"
                                                />
                                            </label>
                                        @else
                                            <span class="text-gray-300 dark:text-gray-700">—</span>
                                        @endif
                                    </td>
                                @endforeach
                                <td class="px-3 py-2.5 text-center">
                                    <x-filament::icon-button
                                        icon="heroicon-m-check-circle"
                                        color="gray"
                                        size="sm"
                                        wire:click="toggleModule('{{ $module }}')"
                                        :tooltip="'전체 토글'"
                                    />
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        {{-- 하단 저장 버튼 --}}
        <div class="flex justify-end">
            <x-filament::button
                wire:click="save"
                icon="heroicon-o-check"
            >
                저장
            </x-filament::button>
        </div>
    </div>
</x-filament-panels::page>
