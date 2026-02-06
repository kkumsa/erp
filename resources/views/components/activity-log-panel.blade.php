@props([
    'subjectType' => null,
    'subjectId' => null,
    'limit' => 20,
])

@php
    $activities = collect();
    if ($subjectType && $subjectId) {
        $activities = \Spatie\Activitylog\Models\Activity::query()
            ->where('subject_type', $subjectType)
            ->where('subject_id', $subjectId)
            ->with('causer')
            ->orderByDesc('created_at')
            ->limit($limit)
            ->get();
    }
@endphp

<div class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
    <div class="fi-section-header flex items-center gap-x-3 overflow-hidden px-6 py-4">
        <div class="flex-1">
            <h3 class="fi-section-header-heading text-base font-semibold leading-6 text-gray-950 dark:text-white flex items-center gap-2">
                <x-heroicon-o-clock class="h-5 w-5 text-gray-400" />
                활동 로그
            </h3>
        </div>
    </div>

    <div class="fi-section-content border-t border-gray-200 dark:border-white/10">
        @if($activities->isEmpty())
            <div class="px-6 py-8 text-center text-sm text-gray-500 dark:text-gray-400">
                기록된 활동이 없습니다.
            </div>
        @else
            <div class="divide-y divide-gray-100 dark:divide-white/5">
                @foreach($activities as $activity)
                    <div class="px-6 py-3 flex items-start gap-3">
                        {{-- 이벤트 아이콘 --}}
                        <div class="mt-0.5 flex-shrink-0">
                            @switch($activity->event)
                                @case('created')
                                    <span class="inline-flex items-center justify-center h-6 w-6 rounded-full bg-emerald-100 dark:bg-emerald-500/10">
                                        <x-heroicon-m-plus class="h-3.5 w-3.5 text-emerald-600 dark:text-emerald-400" />
                                    </span>
                                    @break
                                @case('updated')
                                    <span class="inline-flex items-center justify-center h-6 w-6 rounded-full bg-blue-100 dark:bg-blue-500/10">
                                        <x-heroicon-m-pencil class="h-3.5 w-3.5 text-blue-600 dark:text-blue-400" />
                                    </span>
                                    @break
                                @case('deleted')
                                    <span class="inline-flex items-center justify-center h-6 w-6 rounded-full bg-red-100 dark:bg-red-500/10">
                                        <x-heroicon-m-trash class="h-3.5 w-3.5 text-red-600 dark:text-red-400" />
                                    </span>
                                    @break
                                @default
                                    <span class="inline-flex items-center justify-center h-6 w-6 rounded-full bg-gray-100 dark:bg-gray-500/10">
                                        <x-heroicon-m-information-circle class="h-3.5 w-3.5 text-gray-600 dark:text-gray-400" />
                                    </span>
                            @endswitch
                        </div>

                        {{-- 내용 --}}
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 text-sm">
                                <span class="font-medium text-gray-950 dark:text-white">
                                    {{ $activity->causer?->name ?? '시스템' }}
                                </span>
                                <span class="text-gray-500 dark:text-gray-400">
                                    @switch($activity->event)
                                        @case('created') 생성 @break
                                        @case('updated') 수정 @break
                                        @case('deleted') 삭제 @break
                                        @default {{ $activity->description ?? $activity->event }}
                                    @endswitch
                                </span>
                                <span class="text-xs text-gray-400 dark:text-gray-500 ml-auto whitespace-nowrap">
                                    {{ $activity->created_at->diffForHumans() }}
                                </span>
                            </div>

                            {{-- 변경 사항 --}}
                            @if($activity->event === 'updated' && $activity->properties->has('old') && $activity->properties->has('attributes'))
                                <div class="mt-1.5 text-xs space-y-0.5">
                                    @foreach($activity->properties['attributes'] as $key => $newValue)
                                        @php
                                            $oldValue = $activity->properties['old'][$key] ?? null;
                                        @endphp
                                        @if($oldValue !== $newValue)
                                            <div class="flex items-center gap-1 text-gray-600 dark:text-gray-400">
                                                <span class="font-medium">{{ $key }}</span>:
                                                <span class="line-through text-red-500/70">{{ is_null($oldValue) ? '-' : $oldValue }}</span>
                                                <x-heroicon-m-arrow-right class="h-3 w-3 text-gray-400 flex-shrink-0" />
                                                <span class="text-emerald-600 dark:text-emerald-400">{{ is_null($newValue) ? '-' : $newValue }}</span>
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            @endif

                            {{-- 생성 시 속성 --}}
                            @if($activity->event === 'created' && $activity->properties->has('attributes'))
                                <div class="mt-1.5 text-xs text-gray-500 dark:text-gray-400">
                                    @php
                                        $attrs = collect($activity->properties['attributes'])->filter(fn($v) => !is_null($v) && $v !== '');
                                        $preview = $attrs->take(3)->map(fn($v, $k) => "$k: $v")->join(', ');
                                    @endphp
                                    {{ $preview }}@if($attrs->count() > 3) ... 외 {{ $attrs->count() - 3 }}건 @endif
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
