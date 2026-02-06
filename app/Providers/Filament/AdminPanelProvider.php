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
                '재무/회계',
                '프로젝트',
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
                    </style>
                ')
            )
            ->renderHook(
                PanelsRenderHook::BODY_END,
                fn () => new HtmlString('
                    <script>
                        (function() {
                            function addBackButton() {
                                // 이미 추가되어 있으면 스킵
                                if (document.querySelector(".fi-back-nav-wrapper")) return;

                                // 브레드크럼 찾기
                                const breadcrumbs = document.querySelector(".fi-breadcrumbs");
                                if (!breadcrumbs) return;

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

                            // Livewire 네비게이션 후에도 추가
                            document.addEventListener("livewire:navigated", function() {
                                setTimeout(addBackButton, 50);
                            });
                        })();
                    </script>
                ')
            );
    }
}
