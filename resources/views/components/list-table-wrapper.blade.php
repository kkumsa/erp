@props([])
{{-- 목록 테이블 공통 래퍼: 가로 스크롤 + 슬라이드 패널 바깥 클릭 시 닫기 --}}
<div
    class="fi-list-table-wrapper min-w-0"
    x-data
    @click="
        if (!$wire.slideOverMode || !$wire.selectedRecordId) return;
        const target = $event.target;
        const tr = target.closest('tbody tr');
        if (tr && !tr.querySelector('.fi-ta-group-header')) return;
        if (target.closest('button, a, input, select, textarea, label, [role=button]')) return;
        if (target.closest('.fi-slide-over-panel, [x-ref=panel]')) return;
        window.dispatchEvent(new CustomEvent('panel-close'));
    "
>
    {{ $slot }}
</div>
