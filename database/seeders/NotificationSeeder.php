<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Carbon\Carbon;

class NotificationSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();

        if ($users->isEmpty()) {
            $this->command->warn('사용자가 없습니다. 먼저 사용자를 생성해주세요.');
            return;
        }

        $now = Carbon::now();

        // 조직에 맞는 다양한 알림 샘플
        $notifications = [
            // ── 업무/태스크 ──
            [
                'title' => '태스크 배정',
                'body' => '최개발님이 \'사용자 관리 API 개발\' 태스크를 배정했습니다. (프로젝트: 게임 백오피스 시스템)',
                'icon' => 'heroicon-o-clipboard-document-check',
                'iconColor' => 'info',
                'status' => 'unread',
                'ago' => 5,
            ],
            [
                'title' => '태스크 완료',
                'body' => '\'데이터베이스 스키마 구축\' 태스크가 완료되었습니다. (프로젝트: 게임 백오피스 시스템)',
                'icon' => 'heroicon-o-check-circle',
                'iconColor' => 'success',
                'status' => 'unread',
                'ago' => 15,
            ],
            [
                'title' => '마일스톤 완료',
                'body' => '프로젝트 \'게임 백오피스 시스템\'의 마일스톤 \'1단계: 요구사항 분석\'이 완료되었습니다.',
                'icon' => 'heroicon-o-flag',
                'iconColor' => 'success',
                'status' => 'unread',
                'ago' => 30,
            ],

            // ── 결재/승인 ──
            [
                'title' => '결재 요청 (구매주문)',
                'body' => '오설계님이 구매주문 \'스마트팩토리 서버 부품\'의 승인을 요청했습니다. (금액: ₩28,600,000)',
                'icon' => 'heroicon-o-shopping-cart',
                'iconColor' => 'warning',
                'status' => 'unread',
                'ago' => 10,
            ],
            [
                'title' => '휴가 승인 요청',
                'body' => '강코딩님이 연차 휴가를 신청했습니다. (2026-02-14 ~ 2026-02-16, 3일)',
                'icon' => 'heroicon-o-calendar-days',
                'iconColor' => 'warning',
                'status' => 'unread',
                'ago' => 45,
            ],
            [
                'title' => '비용 청구 승인',
                'body' => '청구하신 비용 \'AWS 클라우드 서비스 1월분\' ₩935,000이 승인되었습니다.',
                'icon' => 'heroicon-o-banknotes',
                'iconColor' => 'success',
                'status' => 'read',
                'ago' => 120,
            ],
            [
                'title' => '결재 완료',
                'body' => '구매주문 \'KAIST GPU 서버 부품\'의 결재가 완료되었습니다. (CEO 최종 승인)',
                'icon' => 'heroicon-o-check-badge',
                'iconColor' => 'success',
                'status' => 'read',
                'ago' => 180,
            ],

            // ── CRM ──
            [
                'title' => '새 리드 배정',
                'body' => '새로운 리드 \'(주)블루헬스\'가 배정되었습니다. (출처: 웹사이트, 예상매출: ₩60,000,000)',
                'icon' => 'heroicon-o-user-plus',
                'iconColor' => 'info',
                'status' => 'unread',
                'ago' => 20,
            ],
            [
                'title' => '영업기회 단계 변경',
                'body' => '영업기회 \'스마트팩토리 IoT 시스템\'의 단계가 \'제안\' → \'협상\'으로 변경되었습니다. (₩150,000,000)',
                'icon' => 'heroicon-o-arrow-trending-up',
                'iconColor' => 'info',
                'status' => 'read',
                'ago' => 240,
            ],

            // ── 재무/재고 ──
            [
                'title' => '송장 연체 알림',
                'body' => '송장 \'결제 API 착수금\'의 결제 기한이 다가옵니다. (잔액: ₩19,250,000, 기한: 7일 후)',
                'icon' => 'heroicon-o-exclamation-triangle',
                'iconColor' => 'danger',
                'status' => 'unread',
                'ago' => 8,
            ],
            [
                'title' => '결제 수신',
                'body' => 'KAIST GPU 서버 1차분 ₩137,500,000 결제가 입금되었습니다. (계좌이체)',
                'icon' => 'heroicon-o-credit-card',
                'iconColor' => 'success',
                'status' => 'read',
                'ago' => 300,
            ],
            [
                'title' => '재고 부족 알림',
                'body' => '상품 \'DDR5 ECC RDIMM 64GB\'의 재고가 부족합니다. (현재: 12개 / 최소: 10개)',
                'icon' => 'heroicon-o-cube',
                'iconColor' => 'warning',
                'status' => 'unread',
                'ago' => 50,
            ],
            [
                'title' => '계약 만료 임박',
                'body' => '\'AI 연구 GPU 서버 납품\' 계약이 30일 후 만료됩니다. (KAIST)',
                'icon' => 'heroicon-o-document-text',
                'iconColor' => 'warning',
                'status' => 'unread',
                'ago' => 35,
            ],
        ];

        $count = 0;

        foreach ($users as $user) {
            // 각 사용자에게 5~8개 랜덤 알림 배정 (전체를 모두 주지 않고 현실적으로)
            $selectedNotifs = collect($notifications)->shuffle()->take(rand(5, 8));

            foreach ($selectedNotifs as $notif) {
                $createdAt = $now->copy()->subMinutes($notif['ago'] + rand(0, 60));

                $data = [
                    'title' => $notif['title'],
                    'body' => $notif['body'],
                    'icon' => $notif['icon'],
                    'iconColor' => $notif['iconColor'],
                    'status' => $notif['status'],
                    'duration' => 'persistent',
                    'format' => 'filament',
                    'actions' => [],
                ];

                $user->notifications()->create([
                    'id' => Str::uuid()->toString(),
                    'type' => 'Filament\\Notifications\\DatabaseNotification',
                    'data' => $data,
                    'read_at' => $notif['status'] === 'read' ? $createdAt->copy()->addMinutes(rand(5, 30)) : null,
                    'created_at' => $createdAt,
                    'updated_at' => $createdAt,
                ]);

                $count++;
            }
        }

        $this->command->info("  알림 시딩 완료: {$count}건 ({$users->count()}명)");
    }
}
