<?php

namespace Database\Seeders;

use App\Models\ApprovalFlow;
use App\Models\ApprovalFlowStep;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class ApprovalFlowSeeder extends Seeder
{
    public function run(): void
    {
        $managerRole = Role::where('name', 'Manager')->first();
        $adminRole = Role::where('name', 'Admin')->first();

        // 1. 구매주문 기본 결재라인 (100만원 미만)
        $flow1 = ApprovalFlow::firstOrCreate(
            [
                'name' => '구매주문 기본 결재 (100만원 미만)',
                'target_type' => 'App\\Models\\PurchaseOrder',
            ],
            [
                'conditions' => ['max_amount' => 1000000],
                'is_default' => false,
                'is_active' => true,
            ]
        );

        if ($flow1->steps()->count() === 0) {
            // 1단계: Manager 승인
            ApprovalFlowStep::create([
                'approval_flow_id' => $flow1->id,
                'step_order' => 1,
                'approver_type' => 'role',
                'approver_id' => $managerRole?->id,
                'action_type' => '승인',
            ]);
        }

        // 2. 구매주문 고액 결재라인 (100만원 이상)
        $flow2 = ApprovalFlow::firstOrCreate(
            [
                'name' => '구매주문 고액 결재 (100만원 이상)',
                'target_type' => 'App\\Models\\PurchaseOrder',
            ],
            [
                'conditions' => ['min_amount' => 1000000],
                'is_default' => false,
                'is_active' => true,
            ]
        );

        if ($flow2->steps()->count() === 0) {
            // 1단계: Manager 승인
            ApprovalFlowStep::create([
                'approval_flow_id' => $flow2->id,
                'step_order' => 1,
                'approver_type' => 'role',
                'approver_id' => $managerRole?->id,
                'action_type' => '승인',
            ]);

            // 2단계: Admin 승인
            ApprovalFlowStep::create([
                'approval_flow_id' => $flow2->id,
                'step_order' => 2,
                'approver_type' => 'role',
                'approver_id' => $adminRole?->id,
                'action_type' => '승인',
            ]);

            // 3단계: 김대표 (대표이사, user id=1) 최종 승인
            ApprovalFlowStep::create([
                'approval_flow_id' => $flow2->id,
                'step_order' => 3,
                'approver_type' => 'user',
                'approver_id' => 1,
                'action_type' => '승인',
            ]);
        }

        // ──── 비용 결재라인 ────

        // 비용 기본 결재 (Manager 1단계)
        $expenseFlow = ApprovalFlow::firstOrCreate(
            [
                'name' => '비용 기본 결재',
                'target_type' => 'App\\Models\\Expense',
            ],
            [
                'conditions' => null,
                'is_default' => true,
                'is_active' => true,
            ]
        );

        if ($expenseFlow->steps()->count() === 0) {
            ApprovalFlowStep::create([
                'approval_flow_id' => $expenseFlow->id,
                'step_order' => 1,
                'approver_type' => 'role',
                'approver_id' => $managerRole?->id,
                'action_type' => '승인',
            ]);
        }

        // ──── 휴가 결재라인 ────

        // 휴가 기본 결재 (Manager 1단계)
        $leaveFlow = ApprovalFlow::firstOrCreate(
            [
                'name' => '휴가 기본 결재',
                'target_type' => 'App\\Models\\Leave',
            ],
            [
                'conditions' => null,
                'is_default' => true,
                'is_active' => true,
            ]
        );

        if ($leaveFlow->steps()->count() === 0) {
            ApprovalFlowStep::create([
                'approval_flow_id' => $leaveFlow->id,
                'step_order' => 1,
                'approver_type' => 'role',
                'approver_id' => $managerRole?->id,
                'action_type' => '승인',
            ]);
        }

        // ──── 근무기록 결재라인 ────

        // 근무기록 기본 결재 (Manager 1단계)
        $timesheetFlow = ApprovalFlow::firstOrCreate(
            [
                'name' => '근무기록 기본 결재',
                'target_type' => 'App\\Models\\Timesheet',
            ],
            [
                'conditions' => null,
                'is_default' => true,
                'is_active' => true,
            ]
        );

        if ($timesheetFlow->steps()->count() === 0) {
            ApprovalFlowStep::create([
                'approval_flow_id' => $timesheetFlow->id,
                'step_order' => 1,
                'approver_type' => 'role',
                'approver_id' => $managerRole?->id,
                'action_type' => '승인',
            ]);
        }

        // ──── 구매주문 기본 결재라인 (fallback) ────

        // 3. 구매주문 기본 결재라인 (조건 미매칭 시 기본 적용)
        $flow3 = ApprovalFlow::firstOrCreate(
            [
                'name' => '구매주문 기본 결재',
                'target_type' => 'App\\Models\\PurchaseOrder',
            ],
            [
                'conditions' => null,
                'is_default' => true,
                'is_active' => true,
            ]
        );

        if ($flow3->steps()->count() === 0) {
            // 1단계: Manager 승인
            ApprovalFlowStep::create([
                'approval_flow_id' => $flow3->id,
                'step_order' => 1,
                'approver_type' => 'role',
                'approver_id' => $managerRole?->id,
                'action_type' => '승인',
            ]);

            // 2단계: Admin 승인
            ApprovalFlowStep::create([
                'approval_flow_id' => $flow3->id,
                'step_order' => 2,
                'approver_type' => 'role',
                'approver_id' => $adminRole?->id,
                'action_type' => '승인',
            ]);
        }
    }
}
