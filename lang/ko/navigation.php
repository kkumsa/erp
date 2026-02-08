<?php

return [

    /*
    |--------------------------------------------------------------------------
    | 네비게이션 그룹
    |--------------------------------------------------------------------------
    */
    'groups' => [
        'dashboard'          => '대시보드',
        'crm'                => 'CRM',
        'project'            => '프로젝트',
        'hr'                 => '인사관리',
        'purchasing'         => '구매관리',
        'finance'            => '재무/회계',
        'inventory'          => '재고관리',
        'inventory_logistics' => '재고/물류',
        'my_settings'        => '내 설정',
        'system_settings'    => '시스템설정',
    ],

    /*
    |--------------------------------------------------------------------------
    | 네비게이션 라벨
    |--------------------------------------------------------------------------
    */
    'labels' => [
        // 대시보드
        'dashboard'             => '대시보드',

        // CRM
        'customer'              => '고객 관리',
        'contact'               => '연락처',
        'lead'                  => '잠재 고객 발굴',
        'opportunity'           => '영업 기회',
        'contract'              => '계약 관리',

        // 프로젝트
        'project'               => '프로젝트 관리',
        'task'                  => '작업',
        'timesheet'             => '타임시트',
        'milestone'             => '마일스톤',

        // 인사관리
        'department'            => '부서 관리',
        'employee'              => '직원 관리',
        'leave'                 => '휴가 관리',
        'attendance'            => '근태 관리',

        // 구매관리
        'supplier'              => '공급업체',
        'purchase_order'        => '구매주문',
        'purchase_order_item'   => '구매주문 항목',

        // 재무/회계
        'payment_matching'      => '결제(청구/입금) 관리',
        'invoice'               => '청구서',
        'invoice_item'          => '청구 항목',
        'payment'               => '결제 내역',
        'bank_deposit'          => '입금 내역',
        'expense'               => '비용 관리',
        'expense_category'      => '비용 카테고리',
        'account'               => '계정과목',

        // 재고관리
        'product'               => '상품 관리',
        'product_category'      => '상품 카테고리',
        'stock'                 => '재고 현황',
        'warehouse'             => '창고 관리',

        // 재고/물류
        'stock_movement'        => '재고 이동',

        // 내 설정
        'my_profile'            => '프로필 관리',
        'notification_settings' => '알림 설정',
        'notification_history'  => '알림 내역',

        // 시스템설정
        'user'                  => '사용자 관리',
        'role'                  => '역할 관리',
        'approval_flow'         => '결재라인 관리',
        'leave_type'            => '휴가 유형',
        'trash'                 => '휴지통',
    ],

];
