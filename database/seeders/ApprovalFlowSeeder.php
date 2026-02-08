<?php

namespace Database\Seeders;

use App\Models\ApprovalFlow;
use App\Models\ApprovalFlowStep;
use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class ApprovalFlowSeeder extends Seeder
{
    public function run(): void
    {
        $managerRole = Role::where('name', 'Manager')->first();
        $adminRole = Role::where('name', 'Admin')->first();
        $ceo = User::where('email', 'admin@techwave.kr')->first();

        $this->command->info('결재라인 시딩 중...');

        // ══════════════════════════════════════════════════
        // ① 사업본부 결재라인 (3단계: 팀장 → 본부장 → CEO)
        // 소프트웨어 본부(SD), 하드웨어 본부(HD) 소속 팀
        // ══════════════════════════════════════════════════

        $targetTypes = [
            'App\\Models\\PurchaseOrder' => '구매주문',
            'App\\Models\\Expense' => '비용',
            'App\\Models\\Leave' => '휴가',
            'App\\Models\\Timesheet' => '타임시트',
        ];

        foreach ($targetTypes as $targetType => $label) {
            // ── 사업본부 기본 결재 (3단계) ──
            $flow3 = ApprovalFlow::firstOrCreate(
                [
                    'name' => "{$label} 결재 (사업본부, 3단계)",
                    'target_type' => $targetType,
                ],
                [
                    'conditions' => null,
                    'is_default' => true,
                    'is_active' => true,
                ]
            );

            if ($flow3->steps()->count() === 0) {
                // 1단계: 팀장(Manager) 승인
                ApprovalFlowStep::create([
                    'approval_flow_id' => $flow3->id,
                    'step_order' => 1,
                    'approver_type' => 'role',
                    'approver_id' => $managerRole?->id,
                    'action_type' => '승인',
                ]);

                // 2단계: 본부장(Admin) 승인
                ApprovalFlowStep::create([
                    'approval_flow_id' => $flow3->id,
                    'step_order' => 2,
                    'approver_type' => 'role',
                    'approver_id' => $adminRole?->id,
                    'action_type' => '승인',
                ]);

                // 3단계: CEO 최종 승인
                ApprovalFlowStep::create([
                    'approval_flow_id' => $flow3->id,
                    'step_order' => 3,
                    'approver_type' => 'user',
                    'approver_id' => $ceo?->id ?? 1,
                    'action_type' => '승인',
                ]);
            }

            // ── CEO 직속팀 결재 (2단계) ──
            // 기획전략실, 경영지원팀: 팀장/실장(Admin) → CEO
            $flow2 = ApprovalFlow::firstOrCreate(
                [
                    'name' => "{$label} 결재 (CEO 직속, 2단계)",
                    'target_type' => $targetType,
                ],
                [
                    'conditions' => null,
                    'is_default' => false,
                    'is_active' => true,
                ]
            );

            if ($flow2->steps()->count() === 0) {
                // 1단계: 팀장/실장(Admin) 승인
                ApprovalFlowStep::create([
                    'approval_flow_id' => $flow2->id,
                    'step_order' => 1,
                    'approver_type' => 'role',
                    'approver_id' => $adminRole?->id,
                    'action_type' => '승인',
                ]);

                // 2단계: CEO 최종 승인
                ApprovalFlowStep::create([
                    'approval_flow_id' => $flow2->id,
                    'step_order' => 2,
                    'approver_type' => 'user',
                    'approver_id' => $ceo?->id ?? 1,
                    'action_type' => '승인',
                ]);
            }

            // ── 구매주문 고액 결재 (1000만원 이상, 사업본부 3단계) ──
            if ($targetType === 'App\\Models\\PurchaseOrder') {
                $flowHigh = ApprovalFlow::firstOrCreate(
                    [
                        'name' => '구매주문 고액 결재 (1,000만원 이상)',
                        'target_type' => $targetType,
                    ],
                    [
                        'conditions' => ['min_amount' => 10000000],
                        'is_default' => false,
                        'is_active' => true,
                    ]
                );

                if ($flowHigh->steps()->count() === 0) {
                    ApprovalFlowStep::create([
                        'approval_flow_id' => $flowHigh->id,
                        'step_order' => 1,
                        'approver_type' => 'role',
                        'approver_id' => $managerRole?->id,
                        'action_type' => '승인',
                    ]);

                    ApprovalFlowStep::create([
                        'approval_flow_id' => $flowHigh->id,
                        'step_order' => 2,
                        'approver_type' => 'role',
                        'approver_id' => $adminRole?->id,
                        'action_type' => '승인',
                    ]);

                    ApprovalFlowStep::create([
                        'approval_flow_id' => $flowHigh->id,
                        'step_order' => 3,
                        'approver_type' => 'user',
                        'approver_id' => $ceo?->id ?? 1,
                        'action_type' => '승인',
                    ]);
                }
            }
        }

        $this->command->info('  ✓ 결재라인 시딩 완료');
        $this->command->info('    - 사업본부 (3단계): 팀장(Manager) → 본부장(Admin) → CEO');
        $this->command->info('    - CEO직속 (2단계): 팀장/실장(Admin) → CEO');
        $this->command->info('    - 구매주문 고액 결재: 1,000만원 이상');
    }
}
