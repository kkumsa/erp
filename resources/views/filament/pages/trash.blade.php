<x-filament-panels::page>
    <div class="space-y-6">
        {{-- 모델 선택 필터 --}}
        <div class="flex flex-wrap items-center gap-2">
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

        {{-- 테이블 --}}
        {{ $this->table }}
    </div>
</x-filament-panels::page>
