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

        // 다양한 알림 샘플 데이터
        $notifications = [
            // 업무 관련
            [
                'title' => '태스크 배정',
                'body' => '김대리님이 \'ERP 대시보드 UI 개선\' 태스크를 배정했습니다.',
                'icon' => 'heroicon-o-clipboard-document-check',
                'iconColor' => 'info',
                'status' => 'unread',
                'ago' => 5, // 분 전
            ],
            [
                'title' => '태스크 완료',
                'body' => '\'API 엔드포인트 리팩토링\' 태스크가 완료되었습니다. (프로젝트: 신규 ERP 시스템)',
                'icon' => 'heroicon-o-check-circle',
                'iconColor' => 'success',
                'status' => 'unread',
                'ago' => 15,
            ],
            [
                'title' => '마일스톤 완료',
                'body' => '프로젝트 \'신규 ERP 시스템\'의 마일스톤 \'Phase 1 - 기본 기능 구현\'이 완료되었습니다.',
                'icon' => 'heroicon-o-flag',
                'iconColor' => 'success',
                'status' => 'unread',
                'ago' => 30,
            ],

            // 승인/결재
            [
                'title' => '휴가 신청',
                'body' => '박기술님이 연차 휴가를 신청했습니다. (2026-02-10 ~ 2026-02-12, 3일)',
                'icon' => 'heroicon-o-calendar-days',
                'iconColor' => 'warning',
                'status' => 'unread',
                'ago' => 45,
            ],
            [
                'title' => '휴가 승인',
                'body' => '신청하신 연차 휴가(2026-02-14 ~ 2026-02-14)가 승인되었습니다.',
                'icon' => 'heroicon-o-calendar-days',
                'iconColor' => 'success',
                'status' => 'read',
                'ago' => 120,
            ],
            [
                'title' => '비용 청구 승인 요청',
                'body' => '이영업님이 비용 ₩350,000을 청구했습니다. (사유: 고객사 미팅 출장비)',
                'icon' => 'heroicon-o-banknotes',
                'iconColor' => 'warning',
                'status' => 'unread',
                'ago' => 60,
            ],
            [
                'title' => '비용 승인',
                'body' => '청구하신 비용 ₩150,000(사무용품 구매)이 승인되었습니다.',
                'icon' => 'heroicon-o-banknotes',
                'iconColor' => 'success',
                'status' => 'read',
                'ago' => 180,
            ],
            [
                'title' => '구매주문 승인 요청',
                'body' => '새로운 구매주문 \'PO-2026-0042\'이 생성되었습니다. 승인이 필요합니다. (공급사: 한국전자, 금액: ₩2,500,000)',
                'icon' => 'heroicon-o-shopping-cart',
                'iconColor' => 'warning',
                'status' => 'unread',
                'ago' => 90,
            ],

            // CRM
            [
                'title' => '새 리드 배정',
                'body' => '새로운 리드 \'(주)테크노솔루션\'이 배정되었습니다. (출처: 웹사이트 문의)',
                'icon' => 'heroicon-o-user-plus',
                'iconColor' => 'info',
                'status' => 'unread',
                'ago' => 20,
            ],
            [
                'title' => '영업기회 단계 변경',
                'body' => '영업기회 \'클라우드 인프라 구축\'의 단계가 \'제안\' → \'협상\'으로 변경되었습니다.',
                'icon' => 'heroicon-o-arrow-trending-up',
                'iconColor' => 'info',
                'status' => 'read',
                'ago' => 240,
            ],

            // 재무/재고
            [
                'title' => '송장 연체 알림',
                'body' => '송장 \'INV-2026-0015\' (고객: (주)대한상사)의 결제 기한이 5일 경과했습니다. 미수금: ₩8,200,000',
                'icon' => 'heroicon-o-exclamation-triangle',
                'iconColor' => 'danger',
                'status' => 'unread',
                'ago' => 10,
            ],
            [
                'title' => '계약 만료 임박',
                'body' => '계약 \'연간 유지보수 계약\' (고객: (주)글로벌테크)이 7일 후 만료됩니다.',
                'icon' => 'heroicon-o-document-text',
                'iconColor' => 'warning',
                'status' => 'unread',
                'ago' => 35,
            ],
            [
                'title' => '재고 부족 알림',
                'body' => '상품 \'서버 메모리 DDR5 32GB\'의 재고가 부족합니다. (현재: 3개 / 최소: 10개)',
                'icon' => 'heroicon-o-cube',
                'iconColor' => 'danger',
                'status' => 'unread',
                'ago' => 50,
            ],
            [
                'title' => '결제 수신',
                'body' => '송장 \'INV-2026-0012\'에 대해 ₩5,000,000 결제가 입금되었습니다. (방법: 계좌이체)',
                'icon' => 'heroicon-o-credit-card',
                'iconColor' => 'success',
                'status' => 'read',
                'ago' => 300,
            ],
        ];

        $count = 0;

        foreach ($users as $user) {
            foreach ($notifications as $notif) {
                $createdAt = $now->copy()->subMinutes($notif['ago']);

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

        $this->command->info("  알림 시딩 완료: {$count}건 ({$users->count()}명 × " . count($notifications) . "개)");
    }
}
