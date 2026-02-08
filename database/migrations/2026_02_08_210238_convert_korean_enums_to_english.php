<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * 모든 한국어 ENUM/상태값을 영문 snake_case로 변환.
 * rollback 시 영문→한국어 역변환.
 */
return new class extends Migration
{
    /**
     * 변환 매핑: [테이블 => [컬럼 => [한국어 => 영문]]]
     */
    protected function getMappings(): array
    {
        return [
            // ===== invoices.status =====
            'invoices' => [
                'status' => [
                    '초안' => 'draft',
                    '발행' => 'issued',
                    '부분결제' => 'partially_paid',
                    '결제완료' => 'paid',
                    '연체' => 'overdue',
                    '취소' => 'cancelled',
                ],
            ],

            // ===== expenses.status =====
            'expenses' => [
                'status' => [
                    '대기' => 'pending',
                    '제출' => 'pending', // 레거시 값
                    '승인요청' => 'approval_requested',
                    '승인' => 'approved',
                    '반려' => 'rejected',
                    '결제완료' => 'paid',
                ],
            ],

            // ===== purchase_orders.status =====
            'purchase_orders' => [
                'status' => [
                    '초안' => 'draft',
                    '승인대기' => 'pending_approval',
                    '승인요청' => 'approval_requested',
                    '승인' => 'approved',
                    '발주완료' => 'ordered',
                    '발주' => 'ordered',
                    '부분입고' => 'partially_received',
                    '입고완료' => 'received',
                    '입고중' => 'partially_received',
                    '완료' => 'completed',
                    '취소' => 'cancelled',
                ],
            ],

            // ===== projects.status =====
            'projects' => [
                'status' => [
                    '계획중' => 'planning',
                    '진행중' => 'in_progress',
                    '보류' => 'on_hold',
                    '완료' => 'completed',
                    '취소' => 'cancelled',
                ],
                'priority' => [
                    '낮음' => 'low',
                    '보통' => 'normal',
                    '높음' => 'high',
                    '긴급' => 'urgent',
                ],
            ],

            // ===== tasks.status & priority =====
            'tasks' => [
                'status' => [
                    '할일' => 'pending',
                    '대기' => 'pending',
                    '진행중' => 'in_progress',
                    '검토중' => 'in_review',
                    '완료' => 'completed',
                    '보류' => 'on_hold',
                ],
                'priority' => [
                    '낮음' => 'low',
                    '보통' => 'normal',
                    '높음' => 'high',
                    '긴급' => 'urgent',
                ],
            ],

            // ===== milestones.status =====
            'milestones' => [
                'status' => [
                    '대기' => 'pending',
                    '진행중' => 'in_progress',
                    '완료' => 'completed',
                    '지연' => 'delayed',
                ],
            ],

            // ===== timesheets.status =====
            'timesheets' => [
                'status' => [
                    '대기' => 'pending',
                    '승인요청' => 'approval_requested',
                    '승인' => 'approved',
                    '반려' => 'rejected',
                ],
            ],

            // ===== leaves.status =====
            'leaves' => [
                'status' => [
                    '대기' => 'pending',
                    '승인요청' => 'approval_requested',
                    '승인' => 'approved',
                    '반려' => 'rejected',
                    '취소' => 'cancelled',
                ],
            ],

            // ===== contracts.status & payment_terms =====
            'contracts' => [
                'status' => [
                    '초안' => 'drafting',
                    '작성중' => 'drafting',
                    '검토중' => 'in_review',
                    '서명완료' => 'pending_signature',
                    '서명대기' => 'pending_signature',
                    '진행중' => 'active',
                    '완료' => 'completed',
                    '해지' => 'terminated',
                ],
                'payment_terms' => [
                    '선불' => 'lump_sum',
                    '일시불' => 'lump_sum',
                    '후불' => 'lump_sum',
                    '분할' => 'installment',
                    '월정액' => 'monthly',
                    '마일스톤' => 'milestone',
                ],
            ],

            // ===== leads.status & source =====
            'leads' => [
                'status' => [
                    '신규' => 'new',
                    '연락중' => 'contacting',
                    '미팅예정' => 'contacting',
                    '적격' => 'qualified',
                    '제안중' => 'qualified',
                    '부적격' => 'unqualified',
                    '전환완료' => 'converted',
                    '전환' => 'converted',
                    '보류' => 'unqualified',
                    '실패' => 'unqualified',
                ],
                'source' => [
                    '웹사이트' => 'website',
                    '소개' => 'referral',
                    '광고' => 'advertisement',
                    '전시회' => 'exhibition',
                    '기타' => 'other',
                ],
            ],

            // ===== opportunities.stage =====
            'opportunities' => [
                'stage' => [
                    '발굴' => 'discovery',
                    '접촉' => 'contact',
                    '제안' => 'proposal',
                    '협상' => 'negotiation',
                    '계약완료' => 'closed_won',
                    '실패' => 'closed_lost',
                ],
            ],

            // ===== employees.employment_type & status =====
            'employees' => [
                'employment_type' => [
                    '정규직' => 'full_time',
                    '계약직' => 'contract',
                    '인턴' => 'intern',
                    '파트타임' => 'part_time',
                ],
                'status' => [
                    '재직' => 'active',
                    '휴직' => 'on_leave',
                    '퇴직' => 'resigned',
                ],
            ],

            // ===== customers.type & status =====
            'customers' => [
                'type' => [
                    '잠재고객' => 'prospect',
                    '고객' => 'customer',
                    'VIP' => 'vip',
                    '휴면' => 'dormant',
                ],
                'status' => [
                    '활성' => 'active',
                    '비활성' => 'inactive',
                ],
            ],

            // ===== suppliers.status & payment_terms =====
            'suppliers' => [
                'status' => [
                    '활성' => 'active',
                    '비활성' => 'inactive',
                ],
                'payment_terms' => [
                    '선불' => 'prepaid',
                    '후불' => 'postpaid',
                    '정산' => 'settlement',
                ],
            ],

            // ===== attendances.status =====
            'attendances' => [
                'status' => [
                    '정상' => 'normal',
                    '지각' => 'late',
                    '조퇴' => 'early_leave',
                    '결근' => 'absent',
                    '휴가' => 'on_leave',
                    '출장' => 'business_trip',
                    '재택' => 'remote',
                ],
            ],

            // ===== accounts.type =====
            'accounts' => [
                'type' => [
                    '자산' => 'asset',
                    '부채' => 'liability',
                    '자본' => 'equity',
                    '수익' => 'revenue',
                    '비용' => 'expense',
                ],
            ],

            // ===== payments.method =====
            'payments' => [
                'method' => [
                    '현금' => 'cash',
                    '카드' => 'card',
                    '계좌이체' => 'bank_transfer',
                    '어음' => 'check',
                    '수표' => 'check',
                    '기타' => 'other',
                ],
            ],

            // ===== stock_movements.type =====
            'stock_movements' => [
                'type' => [
                    '입고' => 'incoming',
                    '출고' => 'outgoing',
                    '조정' => 'adjustment',
                    '이동' => 'transfer',
                    '반품' => 'return_stock',
                ],
            ],

            // ===== approval_requests.status =====
            'approval_requests' => [
                'status' => [
                    '진행중' => 'in_progress',
                    '승인' => 'approved',
                    '반려' => 'rejected',
                    '취소' => 'cancelled',
                ],
            ],

            // ===== approval_actions.action =====
            'approval_actions' => [
                'action' => [
                    '승인' => 'approved',
                    '반려' => 'rejected',
                    '참조확인' => 'acknowledged',
                    '자동스킵' => 'auto_skipped',
                ],
            ],

            // ===== approval_flow_steps.action_type =====
            'approval_flow_steps' => [
                'action_type' => [
                    '승인' => 'approval',
                    '합의' => 'agreement',
                    '참조' => 'reference',
                ],
            ],
        ];
    }

    public function up(): void
    {
        $mappings = $this->getMappings();

        foreach ($mappings as $table => $columns) {
            if (!Schema::hasTable($table)) {
                continue;
            }

            foreach ($columns as $column => $valueMap) {
                // ENUM → string 변환 (MySQL ENUM 제약 해제)
                if ($this->isEnumColumn($table, $column)) {
                    Schema::table($table, function (Blueprint $t) use ($column) {
                        $t->string($column, 50)->nullable()->change();
                    });
                }

                // 한국어 → 영문 변환
                foreach ($valueMap as $korean => $english) {
                    DB::table($table)
                        ->where($column, $korean)
                        ->update([$column => $english]);
                }
            }
        }
    }

    public function down(): void
    {
        $mappings = $this->getMappings();

        foreach ($mappings as $table => $columns) {
            if (!Schema::hasTable($table)) {
                continue;
            }

            foreach ($columns as $column => $valueMap) {
                // 역방향 매핑 생성 (영문 → 한국어, 첫 번째 매핑 우선)
                $reverseMap = [];
                foreach ($valueMap as $korean => $english) {
                    if (!isset($reverseMap[$english])) {
                        $reverseMap[$english] = $korean;
                    }
                }

                // 영문 → 한국어 변환
                foreach ($reverseMap as $english => $korean) {
                    DB::table($table)
                        ->where($column, $english)
                        ->update([$column => $korean]);
                }
            }
        }
    }

    /**
     * 컬럼이 ENUM 타입인지 확인
     */
    protected function isEnumColumn(string $table, string $column): bool
    {
        $type = DB::selectOne(
            "SELECT COLUMN_TYPE FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ? AND COLUMN_NAME = ?",
            [config('database.connections.mysql.database'), $table, $column]
        );

        return $type && str_starts_with($type->COLUMN_TYPE, 'enum');
    }
};
