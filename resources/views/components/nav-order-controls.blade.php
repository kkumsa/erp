<div class="nav-order-controls" id="nav-order-controls">
    <div
        class="nav-order-btn nav-order-toggle-btn"
        id="nav-order-toggle"
        data-label-change="{{ __('common.nav_order.change_order') }}"
        data-label-apply="{{ __('common.nav_order.apply_order') }}"
    >
        <span class="nav-order-toggle-icon" id="nav-order-icon">
            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m21 16-4 4-4-4"/><path d="M17 20V4"/><path d="m3 8 4-4 4 4"/><path d="M7 4v16"/></svg>
        </span>
        <span class="nav-order-toggle-label" id="nav-order-label">{{ __('common.nav_order.change_order') }}</span>
    </div>
    <div class="nav-order-btn nav-order-reset" id="nav-order-reset">
        <svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"/><path d="M3 3v5h5"/></svg>
        {{ __('common.nav_order.reset_order') }}
    </div>
</div>
