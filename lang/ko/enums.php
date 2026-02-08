<?php

return [

    'invoice_status' => [
        'draft' => '초안',
        'issued' => '발행',
        'partially_paid' => '부분결제',
        'paid' => '결제완료',
        'overdue' => '연체',
        'cancelled' => '취소',
    ],

    'expense_status' => [
        'pending' => '대기',
        'approval_requested' => '승인요청',
        'approved' => '승인',
        'rejected' => '반려',
        'paid' => '결제완료',
    ],

    'purchase_order_status' => [
        'draft' => '초안',
        'pending_approval' => '승인대기',
        'approval_requested' => '승인요청',
        'approved' => '승인',
        'ordered' => '발주완료',
        'partially_received' => '부분입고',
        'received' => '입고완료',
        'completed' => '완료',
        'cancelled' => '취소',
    ],

    'project_status' => [
        'planning' => '계획중',
        'in_progress' => '진행중',
        'on_hold' => '보류',
        'completed' => '완료',
        'cancelled' => '취소',
    ],

    'task_status' => [
        'pending' => '대기',
        'in_progress' => '진행중',
        'in_review' => '검토중',
        'completed' => '완료',
        'on_hold' => '보류',
    ],

    'priority' => [
        'low' => '낮음',
        'normal' => '보통',
        'high' => '높음',
        'urgent' => '긴급',
    ],

    'milestone_status' => [
        'pending' => '대기',
        'in_progress' => '진행중',
        'completed' => '완료',
        'delayed' => '지연',
    ],

    'timesheet_status' => [
        'pending' => '대기',
        'approval_requested' => '승인요청',
        'approved' => '승인',
        'rejected' => '반려',
    ],

    'leave_status' => [
        'pending' => '대기',
        'approval_requested' => '승인요청',
        'approved' => '승인',
        'rejected' => '반려',
        'cancelled' => '취소',
    ],

    'contract_status' => [
        'drafting' => '작성중',
        'in_review' => '검토중',
        'pending_signature' => '서명대기',
        'active' => '진행중',
        'completed' => '완료',
        'terminated' => '해지',
    ],

    'contract_payment_terms' => [
        'lump_sum' => '일시불',
        'installment' => '분할',
        'monthly' => '월정액',
        'milestone' => '마일스톤',
    ],

    'lead_status' => [
        'new' => '신규',
        'contacting' => '연락중',
        'qualified' => '적격',
        'unqualified' => '부적격',
        'converted' => '전환',
    ],

    'lead_source' => [
        'website' => '웹사이트',
        'referral' => '소개',
        'advertisement' => '광고',
        'exhibition' => '전시회',
        'other' => '기타',
    ],

    'opportunity_stage' => [
        'discovery' => '발굴',
        'contact' => '접촉',
        'proposal' => '제안',
        'negotiation' => '협상',
        'closed_won' => '계약완료',
        'closed_lost' => '실패',
    ],

    'employment_type' => [
        'full_time' => '정규직',
        'contract' => '계약직',
        'intern' => '인턴',
        'part_time' => '파트타임',
    ],

    'employee_status' => [
        'active' => '재직',
        'on_leave' => '휴직',
        'resigned' => '퇴직',
    ],

    'customer_type' => [
        'prospect' => '잠재고객',
        'customer' => '고객',
        'vip' => 'VIP',
        'dormant' => '휴면',
    ],

    'active_status' => [
        'active' => '활성',
        'inactive' => '비활성',
    ],

    'attendance_status' => [
        'normal' => '정상',
        'late' => '지각',
        'early_leave' => '조퇴',
        'absent' => '결근',
        'on_leave' => '휴가',
        'business_trip' => '출장',
        'remote' => '재택',
    ],

    'account_type' => [
        'asset' => '자산',
        'liability' => '부채',
        'equity' => '자본',
        'revenue' => '수익',
        'expense' => '비용',
    ],

    'payment_method' => [
        'cash' => '현금',
        'card' => '카드',
        'bank_transfer' => '계좌이체',
        'check' => '수표',
        'other' => '기타',
    ],

    'stock_movement_type' => [
        'incoming' => '입고',
        'outgoing' => '출고',
        'adjustment' => '조정',
        'transfer' => '이동',
        'return_stock' => '반품',
    ],

    'approval_status' => [
        'in_progress' => '진행중',
        'approved' => '승인',
        'rejected' => '반려',
        'cancelled' => '취소',
    ],

    'approval_action_type' => [
        'approval' => '승인',
        'agreement' => '합의',
        'reference' => '참조',
    ],

    'approval_action' => [
        'approved' => '승인',
        'rejected' => '반려',
        'acknowledged' => '참조확인',
        'auto_skipped' => '자동스킵',
    ],

    'supplier_payment_terms' => [
        'prepaid' => '선불',
        'postpaid' => '후불',
        'settlement' => '정산',
    ],

];
