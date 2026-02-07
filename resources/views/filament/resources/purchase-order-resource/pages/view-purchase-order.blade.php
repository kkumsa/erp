<x-filament-panels::page>
    {{-- 기본 Infolist --}}
    {{ $this->infolist }}

    {{-- 결재 진행 상태 --}}
    @if($approvalRequest)
        <x-filament::section>
            <x-slot name="heading">
                <div class="flex items-center gap-2">
                    <x-heroicon-o-clipboard-document-check class="w-5 h-5" />
                    결재 진행 현황
                </div>
            </x-slot>
            <x-slot name="description">
                {{ $approvalRequest->flow?->name ?? '결재라인' }}
                ·
                @if($approvalRequest->status === '진행중')
                    <span class="text-warning-600 dark:text-warning-400 font-medium">진행중 ({{ $approvalRequest->current_step }}/{{ $approvalRequest->total_steps }}단계)</span>
                @elseif($approvalRequest->status === '승인')
                    <span class="text-success-600 dark:text-success-400 font-medium">최종 승인</span>
                @elseif($approvalRequest->status === '반려')
                    <span class="text-danger-600 dark:text-danger-400 font-medium">반려</span>
                @else
                    <span class="text-gray-500 font-medium">{{ $approvalRequest->status }}</span>
                @endif
            </x-slot>

            {{-- 신청 정보 --}}
            <div class="mb-6 p-3 bg-gray-50 dark:bg-gray-800 rounded-lg">
                <div class="flex items-center gap-3 text-sm">
                    <span class="text-gray-500">신청자:</span>
                    <span class="font-medium">{{ $approvalRequest->requester?->name ?? '-' }}</span>
                    <span class="text-gray-300 dark:text-gray-600">|</span>
                    <span class="text-gray-500">신청일:</span>
                    <span>{{ $approvalRequest->requested_at?->format('Y-m-d H:i') }}</span>
                    @if($approvalRequest->completed_at)
                        <span class="text-gray-300 dark:text-gray-600">|</span>
                        <span class="text-gray-500">완료일:</span>
                        <span>{{ $approvalRequest->completed_at->format('Y-m-d H:i') }}</span>
                    @endif
                </div>
            </div>

            {{-- 결재 단계 진행도 (스텝퍼 형태) --}}
            <div class="relative">
                @foreach($approvalRequest->flow?->steps ?? [] as $step)
                    @php
                        $action = $approvalRequest->actions->firstWhere('step_order', $step->step_order);
                        $isSkipped = $action && $action->action === '자동스킵';
                        $isCurrent = $approvalRequest->status === '진행중' && $approvalRequest->current_step === $step->step_order;
                        $isCompleted = $action !== null && !$isSkipped;
                        $isPending = !$action && !$isCurrent;
                    @endphp

                    <div class="flex items-start gap-4 {{ !$loop->last ? 'pb-6' : '' }}">
                        {{-- 스텝 인디케이터 --}}
                        <div class="flex flex-col items-center">
                            <div class="flex items-center justify-center w-8 h-8 rounded-full border-2 text-xs font-bold
                                @if($isSkipped)
                                    border-gray-300 bg-gray-100 text-gray-400 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-500 opacity-50
                                @elseif($isCompleted && $action->action === '반려')
                                    border-danger-500 bg-danger-50 text-danger-600 dark:bg-danger-500/10 dark:text-danger-400
                                @elseif($isCompleted)
                                    border-success-500 bg-success-50 text-success-600 dark:bg-success-500/10 dark:text-success-400
                                @elseif($isCurrent)
                                    border-warning-500 bg-warning-50 text-warning-600 dark:bg-warning-500/10 dark:text-warning-400 animate-pulse
                                @else
                                    border-gray-300 bg-gray-50 text-gray-400 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-500
                                @endif
                            ">
                                @if($isSkipped)
                                    <x-heroicon-s-forward class="w-4 h-4" />
                                @elseif($isCompleted && $action->action === '반려')
                                    <x-heroicon-s-x-mark class="w-4 h-4" />
                                @elseif($isCompleted)
                                    <x-heroicon-s-check class="w-4 h-4" />
                                @else
                                    {{ $step->step_order }}
                                @endif
                            </div>
                            @if(!$loop->last)
                                <div class="w-0.5 flex-1 min-h-[1.5rem]
                                    @if($isSkipped) bg-gray-200 dark:bg-gray-700 opacity-50
                                    @elseif($isCompleted) bg-success-300 dark:bg-success-600
                                    @else bg-gray-200 dark:bg-gray-700
                                    @endif
                                "></div>
                            @endif
                        </div>

                        {{-- 스텝 내용 --}}
                        <div class="flex-1 pb-1">
                            <div class="flex items-center gap-2 mb-1">
                                <span class="font-medium text-sm">{{ $step->step_order }}단계</span>

                                {{-- 결재 유형 배지 --}}
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium
                                    @if($step->action_type === '승인') bg-primary-100 text-primary-700 dark:bg-primary-500/10 dark:text-primary-400
                                    @elseif($step->action_type === '합의') bg-info-100 text-info-700 dark:bg-info-500/10 dark:text-info-400
                                    @else bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400
                                    @endif
                                ">
                                    {{ $step->action_type }}
                                </span>

                                {{-- 상태 --}}
                                @if($isSkipped)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-400 dark:bg-gray-700 dark:text-gray-500 line-through">
                                        스킵
                                    </span>
                                @elseif($isCompleted)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium
                                        @if($action->action === '반려') bg-danger-100 text-danger-700 dark:bg-danger-500/10 dark:text-danger-400
                                        @elseif($action->action === '참조확인') bg-info-100 text-info-700 dark:bg-info-500/10 dark:text-info-400
                                        @else bg-success-100 text-success-700 dark:bg-success-500/10 dark:text-success-400
                                        @endif
                                    ">
                                        {{ $action->action }}
                                    </span>
                                @elseif($isCurrent)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-warning-100 text-warning-700 dark:bg-warning-500/10 dark:text-warning-400">
                                        대기중
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-500 dark:bg-gray-700 dark:text-gray-500">
                                        예정
                                    </span>
                                @endif
                            </div>

                            {{-- 승인자 정보 --}}
                            <div class="text-sm text-gray-600 dark:text-gray-400">
                                <span>{{ $step->approver_label }}</span>
                                @if($step->approver_type === 'role')
                                    <span class="text-gray-400 text-xs">(역할)</span>
                                @endif
                            </div>

                            {{-- 액션 기록 --}}
                            @if($isSkipped)
                                <div class="mt-1 text-xs text-gray-400 italic">
                                    신청자가 해당 단계 권한 보유 (자동 스킵)
                                </div>
                            @elseif($isCompleted)
                                <div class="mt-2 p-2 bg-gray-50 dark:bg-gray-800 rounded text-sm">
                                    <div class="flex items-center gap-2 text-gray-500">
                                        <span>{{ $action->approver?->name }}</span>
                                        <span>·</span>
                                        <span>{{ $action->acted_at?->format('Y-m-d H:i') }}</span>
                                    </div>
                                    @if($action->comment)
                                        <div class="mt-1 text-gray-700 dark:text-gray-300">
                                            "{{ $action->comment }}"
                                        </div>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </x-filament::section>
    @endif
</x-filament-panels::page>
