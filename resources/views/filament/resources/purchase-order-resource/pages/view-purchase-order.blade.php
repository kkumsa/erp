<x-filament-panels::page>
    {{-- 기본 Infolist --}}
    {{ $this->infolist }}

    {{-- 결재 진행 상태 --}}
    <div class="mt-6">
        <x-approval-status-panel :record="$record" />
    </div>

    {{-- 활동 로그 --}}
    <div class="mt-6">
        <x-activity-log-panel
            :subject-type="get_class($record)"
            :subject-id="$record->getKey()"
        />
    </div>
</x-filament-panels::page>
