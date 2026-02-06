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
            ->brandName('스타트업 ERP')
            ->brandLogo(null)
            ->favicon(asset('favicon.ico'))
            ->navigationGroups([
                '대시보드',
                '인사관리',
                'CRM',
                '프로젝트',
                '재무/회계',
                '구매관리',
                '재고관리',
                '시스템설정',
            ])
            ->sidebarCollapsibleOnDesktop()
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                \App\Filament\Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                Widgets\AccountWidget::class,
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
            ])
            ->authMiddleware([
                Authenticate::class,
            ])
            ->databaseNotifications()
            ->globalSearchKeyBindings(['command+k', 'ctrl+k'])
            ->renderHook(
                PanelsRenderHook::HEAD_END,
                fn () => new HtmlString('
                    <style>
                        /* 펼친 상태에서 Section description 숨기기 */
                        .fi-section:not(.fi-collapsed) .fi-section-header-description {
                            display: none !important;
                        }

                        /* 스크롤바 공간 미리 확보 - 레이아웃 흔들림 방지 */
                        html {
                            scrollbar-gutter: stable;
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

                        /* 사이드바 네비게이션 그룹 간격 통일 */
                        .fi-sidebar-nav-groups {
                            gap: 0 !important;
                        }
                        .fi-sidebar-group {
                            padding-top: 0.5rem;
                            padding-bottom: 0.5rem;
                        }
                        /* 첫 번째 그룹 외 모든 그룹에 상단 구분선 */
                        .fi-sidebar-group:not(:first-child) {
                            border-top: 1px solid rgb(229 231 235);
                        }
                        .dark .fi-sidebar-group:not(:first-child) {
                            border-top-color: rgb(55 65 81);
                        }

                        /* 테이블을 container query 대상으로 설정 */
                        .fi-ta {
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
                            .fi-pagination .fi-pagination-previous-btn { display: none !important; }
                            .fi-pagination .fi-pagination-next-btn { display: none !important; }
                            .fi-pagination .fi-pagination-items { display: flex !important; }
                        }

                        /* 중간 컨테이너 (≤650px): 페이지당 셀렉터 숨김 */
                        @container (max-width: 650px) {
                            .fi-pagination > div:has(.fi-pagination-records-per-page-select) {
                                display: none !important;
                            }
                        }

                        /* 좁은 컨테이너 (≤480px): 이전/다음 버튼 모드 - 나머지 전부 숨김 */
                        @container (max-width: 480px) {
                            .fi-pagination .fi-pagination-items { display: none !important; }
                            .fi-pagination .fi-pagination-overview { display: none !important; }
                            .fi-pagination > div:has(.fi-pagination-records-per-page-select) { display: none !important; }
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
                    'defaultMode' => 'slide',
                ]),
                scopes: \App\Filament\Resources\ProjectResource\Pages\ListProjects::class,
            )
            ->renderHook(
                TablesRenderHook::TOOLBAR_SEARCH_AFTER,
                fn () => view('components.view-mode-toggle', [
                    'storageKey' => 'customerListViewMode',
                    'wireMethod' => 'setSlideOverMode',
                    'defaultMode' => 'slide',
                ]),
                scopes: \App\Filament\Resources\CustomerResource\Pages\ListCustomers::class,
            )
            ->renderHook(
                TablesRenderHook::TOOLBAR_SEARCH_AFTER,
                fn () => view('components.view-mode-toggle', [
                    'storageKey' => 'opportunityListViewMode',
                    'wireMethod' => 'setSlideOverMode',
                    'defaultMode' => 'slide',
                ]),
                scopes: \App\Filament\Resources\OpportunityResource\Pages\ListOpportunities::class,
            )
            ->renderHook(
                TablesRenderHook::TOOLBAR_SEARCH_AFTER,
                fn () => view('components.view-mode-toggle', [
                    'storageKey' => 'contractListViewMode',
                    'wireMethod' => 'setSlideOverMode',
                    'defaultMode' => 'slide',
                ]),
                scopes: \App\Filament\Resources\ContractResource\Pages\ListContracts::class,
            )
            ->renderHook(
                TablesRenderHook::TOOLBAR_SEARCH_AFTER,
                fn () => view('components.view-mode-toggle', [
                    'storageKey' => 'invoiceListViewMode',
                    'wireMethod' => 'setSlideOverMode',
                    'defaultMode' => 'slide',
                ]),
                scopes: \App\Filament\Resources\InvoiceResource\Pages\ListInvoices::class,
            )
            ->renderHook(
                TablesRenderHook::TOOLBAR_SEARCH_AFTER,
                fn () => view('components.view-mode-toggle', [
                    'storageKey' => 'userListViewMode',
                    'wireMethod' => 'setSlideOverMode',
                    'defaultMode' => 'slide',
                ]),
                scopes: \App\Filament\Resources\UserResource\Pages\ListUsers::class,
            )
            ->renderHook(
                TablesRenderHook::TOOLBAR_SEARCH_AFTER,
                fn () => view('components.view-mode-toggle', [
                    'storageKey' => 'supplierListViewMode',
                    'wireMethod' => 'setSlideOverMode',
                    'defaultMode' => 'slide',
                ]),
                scopes: \App\Filament\Resources\SupplierResource\Pages\ListSuppliers::class,
            )
            ->renderHook(
                TablesRenderHook::TOOLBAR_SEARCH_AFTER,
                fn () => view('components.view-mode-toggle', [
                    'storageKey' => 'productListViewMode',
                    'wireMethod' => 'setSlideOverMode',
                    'defaultMode' => 'slide',
                ]),
                scopes: \App\Filament\Resources\ProductResource\Pages\ListProducts::class,
            )
            ->renderHook(
                TablesRenderHook::TOOLBAR_SEARCH_AFTER,
                fn () => view('components.view-mode-toggle', [
                    'storageKey' => 'leaveListViewMode',
                    'wireMethod' => 'setSlideOverMode',
                    'defaultMode' => 'slide',
                ]),
                scopes: \App\Filament\Resources\LeaveResource\Pages\ListLeaves::class,
            )
            ->renderHook(
                TablesRenderHook::TOOLBAR_SEARCH_AFTER,
                fn () => view('components.view-mode-toggle', [
                    'storageKey' => 'employeeListViewMode',
                    'wireMethod' => 'setSlideOverMode',
                    'defaultMode' => 'slide',
                ]),
                scopes: \App\Filament\Resources\EmployeeResource\Pages\ListEmployees::class,
            )
            ->renderHook(
                TablesRenderHook::TOOLBAR_SEARCH_AFTER,
                fn () => view('components.view-mode-toggle', [
                    'storageKey' => 'departmentListViewMode',
                    'wireMethod' => 'setSlideOverMode',
                    'defaultMode' => 'slide',
                ]),
                scopes: \App\Filament\Resources\DepartmentResource\Pages\ListDepartments::class,
            )
            ->renderHook(
                TablesRenderHook::TOOLBAR_SEARCH_AFTER,
                fn () => view('components.view-mode-toggle', [
                    'storageKey' => 'leadListViewMode',
                    'wireMethod' => 'setSlideOverMode',
                    'defaultMode' => 'slide',
                ]),
                scopes: \App\Filament\Resources\LeadResource\Pages\ListLeads::class,
            )
            ->renderHook(
                TablesRenderHook::TOOLBAR_SEARCH_AFTER,
                fn () => view('components.view-mode-toggle', [
                    'storageKey' => 'expenseListViewMode',
                    'wireMethod' => 'setSlideOverMode',
                    'defaultMode' => 'slide',
                ]),
                scopes: \App\Filament\Resources\ExpenseResource\Pages\ListExpenses::class,
            )
            ->renderHook(
                TablesRenderHook::TOOLBAR_SEARCH_AFTER,
                fn () => view('components.view-mode-toggle', [
                    'storageKey' => 'paymentListViewMode',
                    'wireMethod' => 'setSlideOverMode',
                    'defaultMode' => 'slide',
                ]),
                scopes: \App\Filament\Resources\PaymentResource\Pages\ListPayments::class,
            )
            ->renderHook(
                TablesRenderHook::TOOLBAR_SEARCH_AFTER,
                fn () => view('components.view-mode-toggle', [
                    'storageKey' => 'purchaseOrderListViewMode',
                    'wireMethod' => 'setSlideOverMode',
                    'defaultMode' => 'slide',
                ]),
                scopes: \App\Filament\Resources\PurchaseOrderResource\Pages\ListPurchaseOrders::class,
            )
            ->renderHook(
                TablesRenderHook::TOOLBAR_SEARCH_AFTER,
                fn () => view('components.view-mode-toggle', [
                    'storageKey' => 'warehouseListViewMode',
                    'wireMethod' => 'setSlideOverMode',
                    'defaultMode' => 'slide',
                ]),
                scopes: \App\Filament\Resources\WarehouseResource\Pages\ListWarehouses::class,
            )
            ->renderHook(
                TablesRenderHook::TOOLBAR_SEARCH_AFTER,
                fn () => view('components.view-mode-toggle', [
                    'storageKey' => 'stockListViewMode',
                    'wireMethod' => 'setSlideOverMode',
                    'defaultMode' => 'slide',
                ]),
                scopes: \App\Filament\Resources\StockResource\Pages\ListStocks::class,
            )
            ->renderHook(
                TablesRenderHook::TOOLBAR_SEARCH_AFTER,
                fn () => view('components.view-mode-toggle', [
                    'storageKey' => 'attendanceListViewMode',
                    'wireMethod' => 'setSlideOverMode',
                    'defaultMode' => 'slide',
                ]),
                scopes: \App\Filament\Resources\AttendanceResource\Pages\ListAttendances::class,
            )
            ->renderHook(
                TablesRenderHook::TOOLBAR_SEARCH_AFTER,
                fn () => view('components.view-mode-toggle', [
                    'storageKey' => 'accountListViewMode',
                    'wireMethod' => 'setSlideOverMode',
                    'defaultMode' => 'slide',
                ]),
                scopes: \App\Filament\Resources\AccountResource\Pages\ListAccounts::class,
            )
            ->renderHook(
                TablesRenderHook::TOOLBAR_SEARCH_AFTER,
                fn () => view('components.view-mode-toggle', [
                    'storageKey' => 'leaveTypeListViewMode',
                    'wireMethod' => 'setSlideOverMode',
                    'defaultMode' => 'slide',
                ]),
                scopes: \App\Filament\Resources\LeaveTypeResource\Pages\ListLeaveTypes::class,
            )
            ->renderHook(
                TablesRenderHook::TOOLBAR_SEARCH_AFTER,
                fn () => view('components.view-mode-toggle', [
                    'storageKey' => 'expenseCategoryListViewMode',
                    'wireMethod' => 'setSlideOverMode',
                    'defaultMode' => 'slide',
                ]),
                scopes: \App\Filament\Resources\ExpenseCategoryResource\Pages\ListExpenseCategories::class,
            )
            ->renderHook(
                TablesRenderHook::TOOLBAR_SEARCH_AFTER,
                fn () => view('components.view-mode-toggle', [
                    'storageKey' => 'productCategoryListViewMode',
                    'wireMethod' => 'setSlideOverMode',
                    'defaultMode' => 'slide',
                ]),
                scopes: \App\Filament\Resources\ProductCategoryResource\Pages\ListProductCategories::class,
            )
            ->renderHook(
                PanelsRenderHook::BODY_END,
                fn () => new HtmlString('
                    <script>
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
