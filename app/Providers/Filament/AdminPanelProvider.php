<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Support\Facades\FilamentView;
use Filament\Tables\View\TablesRenderHook;
use Filament\View\PanelsRenderHook;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\HtmlString;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->colors([
                'primary' => Color::Blue,
                'danger' => Color::Red,
                'gray' => Color::Slate,
                'info' => Color::Sky,
                'success' => Color::Emerald,
                'warning' => Color::Orange,
            ])
            ->font('Pretendard')
            ->brandName('스타트업 GRP')
            ->brandLogo(null)
            ->favicon(asset('favicon.ico'))
            ->navigationGroups([
                __('navigation.groups.dashboard'),
                __('navigation.groups.crm'),
                __('navigation.groups.project'),
                __('navigation.groups.hr'),
                __('navigation.groups.purchasing'),
                __('navigation.groups.finance'),
                __('navigation.groups.inventory'),
                __('navigation.groups.inventory_logistics'),
                __('navigation.groups.my_settings'),
                __('navigation.groups.system_settings'),
            ])
            ->sidebarCollapsibleOnDesktop()
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                \App\Filament\Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                // 커스텀 AccountWidget은 discoverWidgets로 자동 등록됨
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
                \App\Http\Middleware\SetLocale::class,
            ])
            ->authMiddleware([
                Authenticate::class,
                \App\Http\Middleware\SetLocale::class,
            ])
            ->databaseNotifications()
            ->globalSearchKeyBindings(['command+k', 'ctrl+k'])
            ->renderHook(
                PanelsRenderHook::GLOBAL_SEARCH_BEFORE,
                fn () => \Livewire\Livewire::mount('locale-switcher'),
            )
            ->renderHook(
                PanelsRenderHook::AUTH_LOGIN_FORM_BEFORE,
                fn () => view('components.login-locale-switcher'),
            )
            ->renderHook(
                PanelsRenderHook::AUTH_LOGIN_FORM_AFTER,
                fn () => new HtmlString(
                    '<div style="text-align:center; margin-top:1rem; font-size:0.75rem; color:rgb(156 163 175);">'
                    . __('common.pages.login_ip') . ': ' . request()->ip()
                    . '</div>'
                ),
            )
            ->renderHook(
                PanelsRenderHook::HEAD_END,
                fn () => new HtmlString('
                    <script>
                        /* 네비게이션 그룹 순서 - 렌더 전 CSS order 즉시 적용 (깜빡임 방지) */
                        (function(){
                            try {
                                var order = JSON.parse(localStorage.getItem("erpNavGroupOrder"));
                                if (!order || !order.length) return;
                                var css = "";
                                for (var i = 0; i < order.length; i++) {
                                    css += ".fi-sidebar-group[data-group-label=" + JSON.stringify(order[i]) + "]{ order:" + i + " !important } ";
                                }
                                var s = document.createElement("style");
                                s.id = "nav-group-order-style";
                                s.textContent = css;
                                document.head.appendChild(s);
                            } catch(e){}
                        })();
                    </script>
                    <style>
                        /* 펼친 상태에서 Section description 숨기기 */
                        .fi-section:not(.fi-collapsed) .fi-section-header-description {
                            display: none !important;
                        }

                        /* 스크롤바 공간 미리 확보 - 레이아웃 흔들림 방지 */
                        html {
                            scrollbar-gutter: stable;
                        }
                        /* 사이드바 네비 영역 레이아웃 안정화 - 메뉴 위치 고정 */
                        .fi-sidebar-nav {
                            contain: layout;
                        }

                        /* 뒤로가기 버튼 + 브레드크럼 wrapper */
                        .fi-back-nav-wrapper {
                            display: flex;
                            align-items: center;
                            margin-bottom: 0.5rem;
                        }
                        .fi-back-button {
                            display: inline-flex;
                            align-items: center;
                            justify-content: center;
                            width: 1.75rem;
                            height: 1.75rem;
                            border-radius: 50%;
                            background-color: rgb(243 244 246);
                            color: rgb(107 114 128);
                            cursor: pointer;
                            transition: all 0.15s ease;
                            margin-right: 1rem;
                            flex-shrink: 0;
                        }
                        .fi-back-button:hover {
                            background-color: rgb(229 231 235);
                            color: rgb(55 65 81);
                        }
                        .dark .fi-back-button {
                            background-color: rgb(55 65 81);
                            color: rgb(156 163 175);
                        }
                        .dark .fi-back-button:hover {
                            background-color: rgb(75 85 99);
                            color: rgb(209 213 219);
                        }
                        .fi-back-nav-wrapper .fi-breadcrumbs {
                            margin-bottom: 0 !important;
                        }

                        /* 사이드바 네비게이션 그룹 간격·구분선 통일 */
                        .fi-sidebar-nav-groups {
                            gap: 0 !important;
                        }
                        .fi-sidebar-group {
                            padding-top: 0.5rem;
                            padding-bottom: 0.5rem;
                            border-top: 1px solid rgb(229 231 235);
                        }
                        .fi-sidebar-group:first-child {
                            border-top: none;
                        }
                        .dark .fi-sidebar-group {
                            border-top-color: rgb(55 65 81);
                        }
                        .dark .fi-sidebar-group:first-child {
                            border-top-color: transparent;
                        }

                        /* 목록 테이블 가로 스크롤 (데스크탑/모바일)
                           - 메인 영역이 flex에서 스크롤 컨테이너가 되도록 min-width: 0 */
                        .fi-main {
                            min-width: 0;
                        }
                        .fi-list-table-wrapper {
                            overflow-x: auto;
                            -webkit-overflow-scrolling: touch;
                        }
                        .fi-list-table-wrapper .fi-ta-ctn {
                            overflow: visible;
                            min-width: min-content;
                        }
                        .fi-list-table-wrapper .fi-ta-content {
                            overflow-x: auto;
                            -webkit-overflow-scrolling: touch;
                        }

                        /* 로그인 정보 위젯: 같은 행의 진행 중인 프로젝트 높이에 맞춤 (나중에 배경 꾸미기용) */
                        .fi-account-widget.h-full {
                            display: flex;
                            min-height: 0;
                        }
                        .fi-account-widget.h-full > * {
                            flex: 1;
                            min-height: 0;
                        }

                        /* 진행 중인 프로젝트 위젯: 테이블 열 제목(thead)만 숨김 */
                        .fi-wi-table-hide-column-headers thead {
                            display: none;
                        }

                        /* 페이지네이션 영역을 container query 대상으로 설정
                           (.fi-ta에 적용하면 containment context가 테이블 전체를 감싸서
                           bulk actions 등 fixed/absolute 요소의 위치가 깨짐) */
                        .fi-ta .fi-pagination {
                            container-type: inline-size;
                        }

                        /*
                         * 페이지네이션 컨테이너 기반 반응형 (3단계)
                         *   넓음 (>650px): [요약] [페이지번호(중앙)] [페이지당(우측)]
                         *   중간 (481~650px): [요약] [페이지번호(중앙)]
                         *   좁음 (≤480px): [이전] ... [다음]
                         */

                        /* 페이지 번호 중앙, 페이지당 셀렉터 우측 */
                        .fi-pagination .fi-pagination-items {
                            grid-column: 2 !important;
                            grid-row: 1 !important;
                            justify-self: center !important;
                        }
                        .fi-pagination > div:has(.fi-pagination-records-per-page-select) {
                            grid-column: 3 !important;
                            grid-row: 1 !important;
                            justify-self: end !important;
                        }

                        /* 넓은 컨테이너 (>480px): 페이지번호 모드 - 이전/다음 단순 버튼 숨김 */
                        @container (min-width: 481px) {
                            .fi-pagination-previous-btn { display: none !important; }
                            .fi-pagination-next-btn { display: none !important; }
                            .fi-pagination-items { display: flex !important; }
                        }

                        /* 중간 컨테이너 (≤650px): 페이지당 셀렉터 숨김 */
                        @container (max-width: 650px) {
                            div:has(> .fi-pagination-records-per-page-select) {
                                display: none !important;
                            }
                        }

                        /* 좁은 컨테이너 (≤480px): 이전/다음 버튼 모드 - 나머지 전부 숨김 */
                        @container (max-width: 480px) {
                            .fi-pagination-items { display: none !important; }
                            .fi-pagination-overview { display: none !important; }
                            div:has(> .fi-pagination-records-per-page-select) { display: none !important; }
                        }

                        /* ─── 사이드바 그룹 드래그앤드롭 정렬 ─── */

                        /* 드래그 핸들 - 기본 숨김, 편집 모드에서만 표시 */
                        .fi-sidebar-group .nav-drag-handle {
                            display: none;
                            align-items: center;
                            justify-content: center;
                            width: 14px;
                            height: 20px;
                            flex-shrink: 0;
                            cursor: grab;
                            color: rgb(156 163 175);
                            transition: color 0.15s ease;
                        }
                        .fi-sidebar-group .nav-drag-handle:hover {
                            color: rgb(107 114 128);
                        }
                        .fi-sidebar-group .nav-drag-handle:active {
                            cursor: grabbing;
                        }
                        .dark .fi-sidebar-group .nav-drag-handle { color: rgb(107 114 128); }
                        .dark .fi-sidebar-group .nav-drag-handle:hover { color: rgb(156 163 175); }

                        /* 편집 모드: 핸들 표시 + 그룹 하이라이트 */
                        .fi-sidebar-nav.nav-reorder-mode .nav-drag-handle {
                            display: flex;
                        }
                        .fi-sidebar-nav.nav-reorder-mode .fi-sidebar-group {
                            border-radius: 0.375rem;
                            transition: background 0.15s ease;
                        }
                        .fi-sidebar-nav.nav-reorder-mode .fi-sidebar-group:hover {
                            background: rgba(0,0,0,0.03);
                        }
                        .dark .fi-sidebar-nav.nav-reorder-mode .fi-sidebar-group:hover {
                            background: rgba(255,255,255,0.03);
                        }
                        /* 편집 모드: 그룹 하위 메뉴 접기 */
                        .fi-sidebar-nav.nav-reorder-mode .fi-sidebar-group-items {
                            display: none !important;
                        }
                        /* 편집 모드: 그룹 전체 드래그 가능 커서 */
                        .fi-sidebar-nav.nav-reorder-mode .fi-sidebar-group {
                            cursor: grab;
                        }
                        .fi-sidebar-nav.nav-reorder-mode .fi-sidebar-group:active {
                            cursor: grabbing;
                        }
                        /* 편집 모드: 그룹 헤더 클릭(펼침/접힘) 비활성 */
                        .fi-sidebar-nav.nav-reorder-mode .fi-sidebar-group-button {
                            pointer-events: none;
                        }
                        /* 편집 모드: 화살표 아이콘 접힌 방향으로 회전 */
                        .fi-sidebar-nav.nav-reorder-mode .fi-sidebar-group-button svg:last-child {
                            transform: rotate(0deg) !important;
                            transition: transform 0.2s ease;
                        }

                        /* 드래그 중 시각 피드백 */
                        .fi-sidebar-group.is-dragging { opacity: 0.4; }
                        .fi-sidebar-group.drag-over-top::before {
                            content: ""; display: block; height: 2px;
                            background: rgb(59 130 246); border-radius: 1px; margin-bottom: -2px;
                        }
                        .fi-sidebar-group.drag-over-bottom::after {
                            content: ""; display: block; height: 2px;
                            background: rgb(59 130 246); border-radius: 1px; margin-top: -2px;
                        }

                        /* 하단 메뉴 순서 컨트롤 영역 */
                        .nav-order-controls {
                            display: flex;
                            flex-direction: column;
                            gap: 0.25rem;
                            margin-top: 0.75rem;
                            padding-top: 0.5rem;
                            border-top: 1px solid rgb(229 231 235);
                            min-height: 4.25rem;
                        }
                        .dark .nav-order-controls { border-top-color: rgb(55 65 81); }

                        .nav-order-btn {
                            display: flex;
                            align-items: center;
                            justify-content: center;
                            gap: 0.375rem;
                            padding: 0.25rem 0.5rem;
                            font-size: 0.7rem;
                            color: rgb(156 163 175);
                            cursor: pointer;
                            border-radius: 0.375rem;
                            transition: all 0.15s ease;
                            user-select: none;
                        }
                        .nav-order-btn:hover {
                            color: rgb(107 114 128);
                            background: rgba(0,0,0,0.04);
                        }
                        .dark .nav-order-btn:hover {
                            color: rgb(156 163 175);
                            background: rgba(255,255,255,0.04);
                        }
                        /* 순서적용 버튼 (편집 모드 활성) */
                        .nav-order-btn.is-editing {
                            color: rgb(59 130 246);
                            background: rgba(59 130 246, 0.08);
                        }
                        .nav-order-btn.is-editing:hover {
                            background: rgba(59 130 246, 0.15);
                        }
                        /* 순서 초기화 버튼 - visibility로 표시만 제어하여 레이아웃 고정 */
                        .nav-order-reset {
                            display: flex;
                            visibility: hidden;
                            pointer-events: none;
                        }
                        .nav-order-reset.is-visible {
                            visibility: visible;
                            pointer-events: auto;
                        }

                        /* 사이드바 네비게이션 클릭 잠금 상태 */
                        .fi-sidebar-nav.is-locked a {
                            cursor: progress !important;
                        }
                        .fi-sidebar-nav a.is-pending {
                            position: relative;
                            opacity: 0.7;
                        }
                        .fi-sidebar-nav a.is-pending::after {
                            content: "";
                            position: absolute;
                            right: 0.75rem;
                            top: 50%;
                            width: 0.75rem;
                            height: 0.75rem;
                            margin-top: -0.375rem;
                            border: 2px solid currentColor;
                            border-top-color: transparent;
                            border-radius: 9999px;
                            animation: fi-nav-spin 1s linear infinite;
                            opacity: 0.8;
                        }
                        @keyframes fi-nav-spin {
                            to { transform: rotate(360deg); }
                        }
                    </style>
                ')
            )
            ->renderHook(
                TablesRenderHook::TOOLBAR_SEARCH_AFTER,
                fn () => view('components.view-mode-toggle', [
                    'storageKey' => 'projectListViewMode',
                    'wireMethod' => 'setSlideOverMode',
                    'defaultMode' => 'page',
                ]),
                scopes: \App\Filament\Resources\ProjectResource\Pages\ListProjects::class,
            )
            ->renderHook(
                TablesRenderHook::TOOLBAR_SEARCH_AFTER,
                fn () => view('components.view-mode-toggle', [
                    'storageKey' => 'customerListViewMode',
                    'wireMethod' => 'setSlideOverMode',
                    'defaultMode' => 'page',
                ]),
                scopes: \App\Filament\Resources\CustomerResource\Pages\ListCustomers::class,
            )
            ->renderHook(
                TablesRenderHook::TOOLBAR_SEARCH_AFTER,
                fn () => view('components.view-mode-toggle', [
                    'storageKey' => 'opportunityListViewMode',
                    'wireMethod' => 'setSlideOverMode',
                    'defaultMode' => 'page',
                ]),
                scopes: \App\Filament\Resources\OpportunityResource\Pages\ListOpportunities::class,
            )
            ->renderHook(
                TablesRenderHook::TOOLBAR_SEARCH_AFTER,
                fn () => view('components.view-mode-toggle', [
                    'storageKey' => 'contractListViewMode',
                    'wireMethod' => 'setSlideOverMode',
                    'defaultMode' => 'page',
                ]),
                scopes: \App\Filament\Resources\ContractResource\Pages\ListContracts::class,
            )
            ->renderHook(
                TablesRenderHook::TOOLBAR_SEARCH_AFTER,
                fn () => view('components.view-mode-toggle', [
                    'storageKey' => 'invoiceListViewMode',
                    'wireMethod' => 'setSlideOverMode',
                    'defaultMode' => 'page',
                ]),
                scopes: \App\Filament\Resources\InvoiceResource\Pages\ListInvoices::class,
            )
            ->renderHook(
                TablesRenderHook::TOOLBAR_SEARCH_AFTER,
                fn () => view('components.view-mode-toggle', [
                    'storageKey' => 'userListViewMode',
                    'wireMethod' => 'setSlideOverMode',
                    'defaultMode' => 'page',
                ]),
                scopes: \App\Filament\Resources\UserResource\Pages\ListUsers::class,
            )
            ->renderHook(
                TablesRenderHook::TOOLBAR_SEARCH_AFTER,
                fn () => view('components.view-mode-toggle', [
                    'storageKey' => 'supplierListViewMode',
                    'wireMethod' => 'setSlideOverMode',
                    'defaultMode' => 'page',
                ]),
                scopes: \App\Filament\Resources\SupplierResource\Pages\ListSuppliers::class,
            )
            ->renderHook(
                TablesRenderHook::TOOLBAR_SEARCH_AFTER,
                fn () => view('components.view-mode-toggle', [
                    'storageKey' => 'productListViewMode',
                    'wireMethod' => 'setSlideOverMode',
                    'defaultMode' => 'page',
                ]),
                scopes: \App\Filament\Resources\ProductResource\Pages\ListProducts::class,
            )
            ->renderHook(
                TablesRenderHook::TOOLBAR_SEARCH_AFTER,
                fn () => view('components.view-mode-toggle', [
                    'storageKey' => 'leaveListViewMode',
                    'wireMethod' => 'setSlideOverMode',
                    'defaultMode' => 'page',
                ]),
                scopes: \App\Filament\Resources\LeaveResource\Pages\ListLeaves::class,
            )
            ->renderHook(
                TablesRenderHook::TOOLBAR_SEARCH_AFTER,
                fn () => view('components.view-mode-toggle', [
                    'storageKey' => 'employeeListViewMode',
                    'wireMethod' => 'setSlideOverMode',
                    'defaultMode' => 'page',
                ]),
                scopes: \App\Filament\Resources\EmployeeResource\Pages\ListEmployees::class,
            )
            ->renderHook(
                TablesRenderHook::TOOLBAR_SEARCH_AFTER,
                fn () => view('components.view-mode-toggle', [
                    'storageKey' => 'departmentListViewMode',
                    'wireMethod' => 'setSlideOverMode',
                    'defaultMode' => 'page',
                ]),
                scopes: \App\Filament\Resources\DepartmentResource\Pages\ListDepartments::class,
            )
            ->renderHook(
                TablesRenderHook::TOOLBAR_SEARCH_AFTER,
                fn () => view('components.view-mode-toggle', [
                    'storageKey' => 'leadListViewMode',
                    'wireMethod' => 'setSlideOverMode',
                    'defaultMode' => 'page',
                ]),
                scopes: \App\Filament\Resources\LeadResource\Pages\ListLeads::class,
            )
            ->renderHook(
                TablesRenderHook::TOOLBAR_SEARCH_AFTER,
                fn () => view('components.view-mode-toggle', [
                    'storageKey' => 'expenseListViewMode',
                    'wireMethod' => 'setSlideOverMode',
                    'defaultMode' => 'page',
                ]),
                scopes: \App\Filament\Resources\ExpenseResource\Pages\ListExpenses::class,
            )
            ->renderHook(
                TablesRenderHook::TOOLBAR_SEARCH_AFTER,
                fn () => view('components.view-mode-toggle', [
                    'storageKey' => 'paymentListViewMode',
                    'wireMethod' => 'setSlideOverMode',
                    'defaultMode' => 'page',
                ]),
                scopes: \App\Filament\Resources\PaymentResource\Pages\ListPayments::class,
            )
            ->renderHook(
                TablesRenderHook::TOOLBAR_SEARCH_AFTER,
                fn () => view('components.view-mode-toggle', [
                    'storageKey' => 'purchaseOrderListViewMode',
                    'wireMethod' => 'setSlideOverMode',
                    'defaultMode' => 'page',
                ]),
                scopes: \App\Filament\Resources\PurchaseOrderResource\Pages\ListPurchaseOrders::class,
            )
            ->renderHook(
                TablesRenderHook::TOOLBAR_SEARCH_AFTER,
                fn () => view('components.view-mode-toggle', [
                    'storageKey' => 'warehouseListViewMode',
                    'wireMethod' => 'setSlideOverMode',
                    'defaultMode' => 'page',
                ]),
                scopes: \App\Filament\Resources\WarehouseResource\Pages\ListWarehouses::class,
            )
            ->renderHook(
                TablesRenderHook::TOOLBAR_SEARCH_AFTER,
                fn () => view('components.view-mode-toggle', [
                    'storageKey' => 'stockListViewMode',
                    'wireMethod' => 'setSlideOverMode',
                    'defaultMode' => 'page',
                ]),
                scopes: \App\Filament\Resources\StockResource\Pages\ListStocks::class,
            )
            ->renderHook(
                TablesRenderHook::TOOLBAR_SEARCH_AFTER,
                fn () => view('components.view-mode-toggle', [
                    'storageKey' => 'attendanceListViewMode',
                    'wireMethod' => 'setSlideOverMode',
                    'defaultMode' => 'page',
                ]),
                scopes: \App\Filament\Resources\AttendanceResource\Pages\ListAttendances::class,
            )
            ->renderHook(
                TablesRenderHook::TOOLBAR_SEARCH_AFTER,
                fn () => view('components.view-mode-toggle', [
                    'storageKey' => 'accountListViewMode',
                    'wireMethod' => 'setSlideOverMode',
                    'defaultMode' => 'page',
                ]),
                scopes: \App\Filament\Resources\AccountResource\Pages\ListAccounts::class,
            )
            ->renderHook(
                TablesRenderHook::TOOLBAR_SEARCH_AFTER,
                fn () => view('components.view-mode-toggle', [
                    'storageKey' => 'leaveTypeListViewMode',
                    'wireMethod' => 'setSlideOverMode',
                    'defaultMode' => 'page',
                ]),
                scopes: \App\Filament\Resources\LeaveTypeResource\Pages\ListLeaveTypes::class,
            )
            ->renderHook(
                TablesRenderHook::TOOLBAR_SEARCH_AFTER,
                fn () => view('components.view-mode-toggle', [
                    'storageKey' => 'expenseCategoryListViewMode',
                    'wireMethod' => 'setSlideOverMode',
                    'defaultMode' => 'page',
                ]),
                scopes: \App\Filament\Resources\ExpenseCategoryResource\Pages\ListExpenseCategories::class,
            )
            ->renderHook(
                TablesRenderHook::TOOLBAR_SEARCH_AFTER,
                fn () => view('components.view-mode-toggle', [
                    'storageKey' => 'productCategoryListViewMode',
                    'wireMethod' => 'setSlideOverMode',
                    'defaultMode' => 'page',
                ]),
                scopes: \App\Filament\Resources\ProductCategoryResource\Pages\ListProductCategories::class,
            )
            ->renderHook(
                PanelsRenderHook::SIDEBAR_NAV_END,
                fn () => new HtmlString(view('components.nav-order-controls')->render())
            )
            ->renderHook(
                PanelsRenderHook::BODY_END,
                fn () => new HtmlString('
                    <script>
                        /* ── 네비게이션 그룹 순서 변경 (토글 모드 + 서버 동기화) ── */
                        (function() {
                            var STORAGE_KEY = "erpNavGroupOrder";
                            var API_BASE = "/admin/api/preferences";
                            var PREF_KEY = "nav_group_order";
                            var dragState = { group: null, label: "" };
                            var saveTimer = null;
                            var serverSynced = false;
                            var handlesAdded = false;
                            var editMode = false;

                            /* ─── 유틸 ─── */
                            function getLocal() {
                                try { var s = localStorage.getItem(STORAGE_KEY); return s ? JSON.parse(s) : null; }
                                catch(e) { return null; }
                            }
                            function setLocal(arr) {
                                if (arr && arr.length) localStorage.setItem(STORAGE_KEY, JSON.stringify(arr));
                                else localStorage.removeItem(STORAGE_KEY);
                            }
                            function csrf() {
                                var m = document.querySelector("meta[name=csrf-token]");
                                return m ? m.getAttribute("content") : "";
                            }

                            /* ─── CSS order 적용 ─── */
                            function applyCss(arr) {
                                var el = document.getElementById("nav-group-order-style");
                                if (!arr || !arr.length) {
                                    if (el) el.remove();
                                    updateResetBtn(false);
                                    return;
                                }
                                var css = "";
                                for (var i = 0; i < arr.length; i++) {
                                    css += ".fi-sidebar-group[data-group-label=" + JSON.stringify(arr[i]) + "]{ order:" + i + " !important } ";
                                }
                                if (!el) { el = document.createElement("style"); el.id = "nav-group-order-style"; document.head.appendChild(el); }
                                el.textContent = css;
                                updateResetBtn(true);
                            }

                            /* ─── 서버 저장 (디바운스 500ms) ─── */
                            function saveServer(arr) {
                                clearTimeout(saveTimer);
                                saveTimer = setTimeout(function() {
                                    fetch(API_BASE, {
                                        method: "POST",
                                        headers: { "Content-Type": "application/json", "X-CSRF-TOKEN": csrf(), "Accept": "application/json" },
                                        body: JSON.stringify({ key: PREF_KEY, value: arr })
                                    }).catch(function(){});
                                }, 500);
                            }

                            /* ─── 저장 + 반영 ─── */
                            function save(arr) {
                                setLocal(arr);
                                applyCss(arr);
                                saveServer(arr);
                            }

                            /* ─── 순서 초기화 ─── */
                            function resetOrder() {
                                setLocal(null);
                                applyCss(null);
                                saveServer(null);
                                exitEditMode();
                            }

                            /* ─── 서버 동기화 (최초 1회) ─── */
                            function syncServer() {
                                if (serverSynced) return;
                                serverSynced = true;
                                fetch(API_BASE + "/" + encodeURIComponent(PREF_KEY), { headers: { "Accept": "application/json" } })
                                    .then(function(r) { return r.ok ? r.json() : Promise.reject(); })
                                    .then(function(data) {
                                        var sv = data.value;
                                        if (sv && Array.isArray(sv) && sv.length) {
                                            setLocal(sv);
                                            applyCss(sv);
                                        } else {
                                            var lc = getLocal();
                                            if (lc && lc.length) saveServer(lc);
                                        }
                                    })
                                    .catch(function(){});
                            }

                            /* ─── 시각적 순서 (CSS order 기준) ─── */
                            function visualOrder() {
                                var c = document.querySelector(".fi-sidebar-nav-groups");
                                if (!c) return [];
                                var gs = Array.from(c.querySelectorAll(":scope > .fi-sidebar-group"));
                                gs.sort(function(a, b) { return (parseInt(getComputedStyle(a).order) || 0) - (parseInt(getComputedStyle(b).order) || 0); });
                                return gs.map(function(g) { return g.dataset.groupLabel || ""; }).filter(Boolean);
                            }

                            /* ─── 편집 모드 전환 ─── */
                            function setGroupsDraggable(v) {
                                var c = document.querySelector(".fi-sidebar-nav-groups");
                                if (!c) return;
                                c.querySelectorAll(":scope > .fi-sidebar-group").forEach(function(g) {
                                    if (v) g.setAttribute("draggable", "true");
                                    else g.removeAttribute("draggable");
                                });
                            }

                            function enterEditMode() {
                                editMode = true;
                                var nav = document.querySelector(".fi-sidebar-nav");
                                if (nav) nav.classList.add("nav-reorder-mode");
                                setGroupsDraggable(true);
                                updateToggleBtn();
                            }

                            function exitEditMode() {
                                editMode = false;
                                var nav = document.querySelector(".fi-sidebar-nav");
                                if (nav) nav.classList.remove("nav-reorder-mode");
                                setGroupsDraggable(false);
                                updateToggleBtn();
                            }

                            function toggleEditMode() {
                                if (editMode) {
                                    /* 순서적용: 현재 CSS order 기준 순서를 저장 */
                                    var order = visualOrder();
                                    if (order.length) save(order);
                                    exitEditMode();
                                } else {
                                    enterEditMode();
                                }
                            }

                            /* ─── 토글 버튼 UI 업데이트 ─── */
                            function updateToggleBtn() {
                                var btn = document.getElementById("nav-order-toggle");
                                var icon = document.getElementById("nav-order-icon");
                                var label = document.getElementById("nav-order-label");
                                if (!btn) return;
                                var changeLabel = btn.getAttribute("data-label-change") || "순서 변경";
                                var applyLabel = btn.getAttribute("data-label-apply") || "순서 적용";
                                if (editMode) {
                                    btn.classList.add("is-editing");
                                    if (icon) icon.innerHTML = "<svg xmlns=\"http://www.w3.org/2000/svg\" width=\"12\" height=\"12\" viewBox=\"0 0 24 24\" fill=\"none\" stroke=\"currentColor\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\"><path d=\"M20 6 9 17l-5-5\"/></svg>";
                                    if (label) label.textContent = applyLabel;
                                } else {
                                    btn.classList.remove("is-editing");
                                    if (icon) icon.innerHTML = "<svg xmlns=\"http://www.w3.org/2000/svg\" width=\"12\" height=\"12\" viewBox=\"0 0 24 24\" fill=\"none\" stroke=\"currentColor\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\"><path d=\"m21 16-4 4-4-4\"/><path d=\"M17 20V4\"/><path d=\"m3 8 4-4 4 4\"/><path d=\"M7 4v16\"/></svg>";
                                    if (label) label.textContent = changeLabel;
                                }
                            }

                            /* ─── 리셋 버튼 표시/숨김 ─── */
                            function updateResetBtn(hasOrder) {
                                var b = document.getElementById("nav-order-reset");
                                if (b) b.classList.toggle("is-visible", hasOrder);
                            }

                            /* ─── 사이드바 준비 확인 ─── */
                            function sidebarReady() {
                                return !!document.querySelector(".fi-sidebar-nav-groups");
                            }

                            /* ─── 그룹에 핸들 없는지 확인 ─── */
                            function needsHandles() {
                                var btns = document.querySelectorAll(".fi-sidebar-group .fi-sidebar-group-button");
                                if (!btns.length) return false;
                                for (var i = 0; i < btns.length; i++) {
                                    if (!btns[i].querySelector(".nav-drag-handle")) return true;
                                }
                                return false;
                            }

                            /* ─── 하단 컨트롤 영역 (렌더 훅으로 이미 HTML 존재, 이벤트만 연결) ─── */
                            var controlsBound = false;
                            function bindControls() {
                                if (controlsBound) return;
                                var toggleBtn = document.getElementById("nav-order-toggle");
                                var resetBtn = document.getElementById("nav-order-reset");
                                if (!toggleBtn) return;

                                toggleBtn.addEventListener("click", toggleEditMode);
                                if (resetBtn) resetBtn.addEventListener("click", resetOrder);
                                controlsBound = true;
                                updateResetBtn(!!getLocal());
                            }

                            /* ─── 드래그앤드롭 핸들 추가 ─── */
                            function addHandles() {
                                var container = document.querySelector(".fi-sidebar-nav-groups");
                                if (!container) return;

                                var groups = container.querySelectorAll(":scope > .fi-sidebar-group");
                                groups.forEach(function(group) {
                                    var hdr = group.querySelector(".fi-sidebar-group-button");
                                    if (!hdr || hdr.querySelector(".nav-drag-handle")) return;

                                    /* 핸들 생성 (시각적 표시만, 드래그는 그룹 전체에서 가능) */
                                    var h = document.createElement("div");
                                    h.className = "nav-drag-handle";
                                    h.innerHTML = "<svg xmlns=\"http://www.w3.org/2000/svg\" width=\"10\" height=\"10\" viewBox=\"0 0 24 24\" fill=\"currentColor\"><circle cx=\"8\" cy=\"4\" r=\"2\"/><circle cx=\"16\" cy=\"4\" r=\"2\"/><circle cx=\"8\" cy=\"12\" r=\"2\"/><circle cx=\"16\" cy=\"12\" r=\"2\"/><circle cx=\"8\" cy=\"20\" r=\"2\"/><circle cx=\"16\" cy=\"20\" r=\"2\"/></svg>";

                                    /* dragstart — 편집 모드에서만 허용 */
                                    group.addEventListener("dragstart", function(e) {
                                        if (!editMode) { e.preventDefault(); return; }
                                        dragState.group = group;
                                        dragState.label = group.dataset.groupLabel || "";
                                        group.classList.add("is-dragging");
                                        e.dataTransfer.effectAllowed = "move";
                                        e.dataTransfer.setData("text/plain", dragState.label);
                                    });

                                    /* dragend */
                                    group.addEventListener("dragend", function() {
                                        group.classList.remove("is-dragging");
                                        dragState.group = null;
                                        dragState.label = "";
                                        container.querySelectorAll(".drag-over-top,.drag-over-bottom").forEach(function(g) {
                                            g.classList.remove("drag-over-top", "drag-over-bottom");
                                        });
                                    });

                                    /* dragover */
                                    group.addEventListener("dragover", function(e) {
                                        if (!dragState.group || dragState.group === group) return;
                                        e.preventDefault();
                                        e.dataTransfer.dropEffect = "move";
                                        var mid = group.getBoundingClientRect().top + group.getBoundingClientRect().height / 2;
                                        group.classList.remove("drag-over-top", "drag-over-bottom");
                                        group.classList.add(e.clientY < mid ? "drag-over-top" : "drag-over-bottom");
                                    });

                                    /* dragleave */
                                    group.addEventListener("dragleave", function(e) {
                                        if (!group.contains(e.relatedTarget)) {
                                            group.classList.remove("drag-over-top", "drag-over-bottom");
                                        }
                                    });

                                    /* drop — 순서 계산 후 CSS 적용 (아직 서버 저장 안함 — 순서적용 클릭 시 저장) */
                                    group.addEventListener("drop", function(e) {
                                        e.preventDefault();
                                        if (!dragState.group || dragState.group === group) return;
                                        group.classList.remove("drag-over-top", "drag-over-bottom");

                                        var tgtLabel = group.dataset.groupLabel || "";
                                        var order = visualOrder();
                                        var from = order.indexOf(dragState.label);
                                        if (from === -1) return;
                                        order.splice(from, 1);

                                        var to = order.indexOf(tgtLabel);
                                        if (to === -1) return;

                                        var mid = group.getBoundingClientRect().top + group.getBoundingClientRect().height / 2;
                                        if (e.clientY >= mid) to++;
                                        order.splice(to, 0, dragState.label);

                                        /* CSS만 즉시 적용 (로컬/서버 저장은 "순서적용" 클릭 시) */
                                        setLocal(order);
                                        applyCss(order);
                                    });

                                    hdr.insertBefore(h, hdr.firstChild);
                                });

                                bindControls();
                                handlesAdded = true;
                            }

                            /* (draggable은 enterEditMode/exitEditMode에서 일괄 관리) */

                            /* ─── 초기화 & 폴링 ─── */
                            var bootAttempts = 0;
                            function boot() {
                                if (!sidebarReady()) {
                                    if (++bootAttempts < 40) setTimeout(boot, 150);
                                    return;
                                }
                                bindControls();
                                if (needsHandles()) addHandles();
                                syncServer();
                            }

                            /* ─── DOM 변경 감지 (디바운스로 깜빡임 감소) ─── */
                            var obsTimer = null;
                            var obs = new MutationObserver(function() {
                                if (obsTimer) clearTimeout(obsTimer);
                                obsTimer = setTimeout(function() {
                                    obsTimer = null;
                                    if (!controlsBound) bindControls();
                                    if (needsHandles()) addHandles();
                                }, 180);
                            });

                            function observe() {
                                var target = document.querySelector(".fi-sidebar");
                                if (target) obs.observe(target, { childList: true, subtree: true });
                            }

                            /* 초기 실행 */
                            boot();
                            observe();

                            /* Livewire 네비게이션 후 재실행 - 짧은 지연으로 DOM 준비 후 한 번만 실행 */
                            document.addEventListener("livewire:navigated", function() {
                                handlesAdded = false;
                                controlsBound = false;
                                editMode = false;
                                bootAttempts = 0;
                                var t = setTimeout(function() { boot(); observe(); }, 50);
                            });
                        })();

                        /* ── 네비게이션 쓰로틀링 & 뒤로가기 버튼 ── */
                        (function() {
                            let isNavigating = false;
                            let navQueue = [];
                            let pendingLink = null;
                            let unlockTimer = null;

                            function getSidebarElements() {
                                return {
                                    sidebar: document.querySelector(".fi-sidebar"),
                                    sidebarNav: document.querySelector(".fi-sidebar-nav"),
                                };
                            }

                            function setNavLock(locked) {
                                const { sidebar, sidebarNav } = getSidebarElements();
                                if (sidebar) sidebar.classList.toggle("is-locked", locked);
                                if (sidebarNav) sidebarNav.classList.toggle("is-locked", locked);
                            }

                            function clearPendingLink() {
                                if (pendingLink) {
                                    pendingLink.classList.remove("is-pending");
                                    pendingLink = null;
                                }
                            }

                            function markPendingLink(link) {
                                if (!link) return;
                                clearPendingLink();
                                pendingLink = link;
                                pendingLink.classList.add("is-pending");
                            }

                            function markPendingLinkByUrl(url) {
                                if (!url) return;
                                const nav = document.querySelector(".fi-sidebar-nav");
                                if (!nav) return;
                                const link = nav.querySelector(`a[href="${url}"]`);
                                if (link) {
                                    markPendingLink(link);
                                }
                            }

                            function enqueueNav(url, link) {
                                if (!url) return;
                                const last = navQueue[navQueue.length - 1];
                                if (last === url) return;
                                navQueue.push(url);
                                if (link) {
                                    markPendingLink(link);
                                } else {
                                    markPendingLinkByUrl(url);
                                }
                            }

                            function navigateTo(url) {
                                if (!url) return;
                                if (window.Livewire && typeof Livewire.navigate === "function") {
                                    Livewire.navigate(url);
                                } else {
                                    window.location.href = url;
                                }
                            }

                            function addBackButton() {
                                // 브레드크럼 찾기 (wrapper 안에 있지 않은 것)
                                const breadcrumbs = document.querySelector(".fi-breadcrumbs:not(.fi-back-nav-wrapper .fi-breadcrumbs)");
                                if (!breadcrumbs) return;
                                
                                // 이미 wrapper 안에 있으면 스킵
                                if (breadcrumbs.closest(".fi-back-nav-wrapper")) return;

                                // wrapper div 생성
                                const wrapper = document.createElement("div");
                                wrapper.className = "fi-back-nav-wrapper";

                                // 뒤로가기 버튼 생성
                                const backButton = document.createElement("button");
                                backButton.type = "button";
                                backButton.className = "fi-back-button";
                                backButton.title = "뒤로가기";
                                backButton.innerHTML = `<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width: 1rem; height: 1rem;"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" /></svg>`;

                                backButton.addEventListener("click", function(e) {
                                    e.preventDefault();
                                    history.back();
                                });

                                // 브레드크럼 앞에 wrapper 삽입
                                breadcrumbs.parentNode.insertBefore(wrapper, breadcrumbs);
                                // wrapper 안에 버튼과 브레드크럼 넣기
                                wrapper.appendChild(backButton);
                                wrapper.appendChild(breadcrumbs);
                            }

                            // 초기 로드
                            if (document.readyState === "loading") {
                                document.addEventListener("DOMContentLoaded", addBackButton);
                            } else {
                                addBackButton();
                            }

                            // Livewire 이벤트들 처리
                            document.addEventListener("livewire:navigated", function() {
                                setTimeout(addBackButton, 50);
                            });
                            
                            document.addEventListener("livewire:init", function() {
                                setTimeout(addBackButton, 100);
                            });

                            // MutationObserver로 DOM 변화 감지
                            const observer = new MutationObserver(function(mutations) {
                                // 브레드크럼이 wrapper 밖에 있는지 확인
                                const breadcrumbs = document.querySelector(".fi-breadcrumbs:not(.fi-back-nav-wrapper .fi-breadcrumbs)");
                                if (breadcrumbs && !breadcrumbs.closest(".fi-back-nav-wrapper")) {
                                    addBackButton();
                                }
                            });
                            
                            observer.observe(document.body, {
                                childList: true,
                                subtree: true
                            });

                            document.addEventListener("click", function(e) {
                                const link = e.target.closest("a");
                                if (!link) return;
                                if (!link.href) return;
                                if (e.metaKey || e.ctrlKey || e.shiftKey || e.altKey) return;
                                if (link.target === "_blank") return;
                                if (!link.closest(".fi-sidebar-nav")) return;
                                if (!link.href || link.href === window.location.href) return;

                                if (isNavigating) {
                                    enqueueNav(link.href, link);
                                    e.preventDefault();
                                    e.stopPropagation();
                                    return;
                                }

                                isNavigating = true;
                                navQueue = [];
                                clearPendingLink();
                                setNavLock(true);

                                clearTimeout(unlockTimer);
                                unlockTimer = setTimeout(function() {
                                    isNavigating = false;
                                    setNavLock(false);
                                    clearPendingLink();

                                    const nextUrl = navQueue.shift();
                                    if (nextUrl && nextUrl !== window.location.href) {
                                        isNavigating = true;
                                        setNavLock(true);
                                        markPendingLinkByUrl(nextUrl);
                                        navigateTo(nextUrl);
                                    }
                                }, 5000);
                            }, true);

                            document.addEventListener("livewire:navigated", function() {
                                clearTimeout(unlockTimer);
                                isNavigating = false;
                                setNavLock(false);
                                clearPendingLink();

                                const nextUrl = navQueue.shift();
                                if (nextUrl && nextUrl !== window.location.href) {
                                    isNavigating = true;
                                    setNavLock(true);
                                    markPendingLinkByUrl(nextUrl);
                                    navigateTo(nextUrl);
                                }
                            });
                        })();
                    </script>
                ')
            );
    }
}
