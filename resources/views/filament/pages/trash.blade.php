<x-filament-panels::page>
    <div class="space-y-6">
        {{-- 모델 선택 필터 --}}
        <div class="flex flex-wrap items-center gap-2">
            <button
                wire:click="$set('selectedModel', '__all__')"
                type="button"
                @class([
                    'inline-flex items-center gap-x-1 rounded-lg px-3 py-1.5 text-sm font-medium transition',
                    'bg-primary-600 text-white shadow-sm' => $selectedModel === '__all__',
                    'bg-white text-gray-700 ring-1 ring-gray-300 hover:bg-gray-50 dark:bg-gray-900 dark:text-gray-300 dark:ring-gray-700 dark:hover:bg-gray-800' => $selectedModel !== '__all__',
                ])
            >
                {{ __('common.search.all') }} ({{ $this->getTotalTrashedCount() }})
            </button>
            @foreach($this->getModelOptions() as $value => $label)
                <button
                    wire:click="$set('selectedModel', '{{ addslashes($value) }}')"
                    type="button"
                    @class([
                        'inline-flex items-center gap-x-1 rounded-lg px-3 py-1.5 text-sm font-medium transition',
                        'bg-primary-600 text-white shadow-sm' => $selectedModel === $value,
                        'bg-white text-gray-700 ring-1 ring-gray-300 hover:bg-gray-50 dark:bg-gray-900 dark:text-gray-300 dark:ring-gray-700 dark:hover:bg-gray-800' => $selectedModel !== $value,
                    ])
                >
                    {{ $label }}
                </button>
            @endforeach
        </div>

        @if($selectedModel === '__all__')
            {{-- 전체 모드: 커스텀 테이블 --}}
            @php
                $allRecords = $this->getAllTrashedRecords();
            @endphp

            @if($allRecords->isEmpty())
                <div class="fi-ta-empty-state flex flex-1 flex-col items-center justify-center p-6 mx-auto text-center">
                    <div class="fi-ta-empty-state-icon-ctn mb-4">
                        <x-heroicon-o-trash class="h-12 w-12 text-gray-400 dark:text-gray-500" />
                    </div>
                    <h4 class="fi-ta-empty-state-heading text-base font-semibold text-gray-950 dark:text-white">{{ __('common.empty_states.no_deleted_items') }}</h4>
                    <p class="fi-ta-empty-state-description text-sm text-gray-500 dark:text-gray-400 mt-1">{{ __('common.empty_states.trash_empty') }}</p>
                </div>
            @else
                <div class="fi-list-table-wrapper overflow-x-auto min-w-0">
                    <div class="fi-ta rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
                    <table class="fi-ta-table w-full table-auto divide-y divide-gray-200 text-start dark:divide-white/5" style="min-width: min-content;">
                        <thead>
                            <tr class="bg-gray-50 dark:bg-white/5">
                                <th class="fi-ta-header-cell px-4 py-3 text-start text-sm font-semibold text-gray-950 dark:text-white">{{ __('common.trash_page.type_col') }}</th>
                                <th class="fi-ta-header-cell px-4 py-3 text-start text-sm font-semibold text-gray-950 dark:text-white">{{ __('common.trash_page.name_col') }}</th>
                                <th class="fi-ta-header-cell px-4 py-3 text-start text-sm font-semibold text-gray-950 dark:text-white">{{ __('common.trash_page.detail_1') }}</th>
                                <th class="fi-ta-header-cell px-4 py-3 text-start text-sm font-semibold text-gray-950 dark:text-white">{{ __('common.trash_page.detail_2') }}</th>
                                <th class="fi-ta-header-cell px-4 py-3 text-start text-sm font-semibold text-gray-950 dark:text-white">{{ __('fields.deleted_at') }}</th>
                                <th class="fi-ta-header-cell px-4 py-3 text-end text-sm font-semibold text-gray-950 dark:text-white">{{ __('common.table.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-white/5">
                            @foreach($allRecords as $record)
                                <tr class="hover:bg-gray-50 dark:hover:bg-white/5 transition" wire:key="all-{{ $record->_model_class }}-{{ $record->getKey() }}">
                                    <td class="fi-ta-cell px-4 py-3 text-sm">
                                        <span class="inline-flex items-center rounded-md bg-gray-100 px-2 py-1 text-xs font-medium text-gray-600 dark:bg-gray-700 dark:text-gray-400">
                                            {{ $record->_model_label }}
                                        </span>
                                    </td>
                                    <td class="fi-ta-cell px-4 py-3 text-sm text-gray-950 dark:text-white">{{ $record->_display_name }}</td>
                                    <td class="fi-ta-cell px-4 py-3 text-sm text-gray-500 dark:text-gray-400">{{ $record->_detail_1 }}</td>
                                    <td class="fi-ta-cell px-4 py-3 text-sm text-gray-500 dark:text-gray-400">{{ $record->_detail_2 }}</td>
                                    <td class="fi-ta-cell px-4 py-3 text-sm text-gray-500 dark:text-gray-400">{{ $record->deleted_at->format('Y-m-d H:i') }}</td>
                                    <td class="fi-ta-cell px-4 py-3 text-end">
                                        <div class="flex items-center justify-end gap-2">
                                            <button
                                                wire:click="restoreRecord('{{ addslashes($record->_model_class) }}', {{ $record->getKey() }})"
                                                wire:confirm="{{ __('common.confirmations.restore') }}"
                                                class="inline-flex items-center gap-1 rounded-lg px-2 py-1 text-xs font-medium text-success-600 hover:bg-success-50 dark:text-success-400 dark:hover:bg-success-500/10 transition"
                                                title="{{ __('common.buttons.restore') }}"
                                            >
                                                <x-heroicon-o-arrow-uturn-left class="h-4 w-4" />
                                                {{ __('common.buttons.restore') }}
                                            </button>
                                            <button
                                                wire:click="forceDeleteRecord('{{ addslashes($record->_model_class) }}', {{ $record->getKey() }})"
                                                wire:confirm="{{ __('common.confirmations.force_delete') }}"
                                                class="inline-flex items-center gap-1 rounded-lg px-2 py-1 text-xs font-medium text-danger-600 hover:bg-danger-50 dark:text-danger-400 dark:hover:bg-danger-500/10 transition"
                                                title="{{ __('common.buttons.force_delete') }}"
                                            >
                                                <x-heroicon-o-x-circle class="h-4 w-4" />
                                                {{ __('common.buttons.delete') }}
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    </div>
                </div>
            @endif
        @else
            {{-- 개별 모델 모드: Filament 테이블 --}}
            <div class="fi-list-table-wrapper overflow-x-auto min-w-0">
                {{ $this->table }}
            </div>
        @endif
    </div>
</x-filament-panels::page>
