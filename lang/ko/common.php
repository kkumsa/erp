<?php

return [

    /*
    |--------------------------------------------------------------------------
    | 버튼/액션 라벨
    |--------------------------------------------------------------------------
    */
    'buttons' => [
        'save' => '저장',
        'cancel' => '취소',
        'delete' => '삭제',
        'create' => '생성',
        'edit' => '수정',
        'update' => '업데이트',
        'view' => '보기',
        'restore' => '복원',
        'force_delete' => '영구 삭제',
        'approve' => '승인',
        'reject' => '반려',
        'submit' => '제출',
        'search' => '검색',
        'filter' => '필터',
        'reset' => '초기화',
        'export' => '내보내기',
        'import' => '가져오기',
        'print' => '인쇄',
        'download' => '다운로드',
        'upload' => '업로드',
        'close' => '닫기',
        'confirm' => '확인',
        'back' => '뒤로',
        'next' => '다음',
        'previous' => '이전',
        'select' => '선택',
        'select_all' => '전체 선택',
        'deselect_all' => '전체 해제',
        'add' => '추가',
        'remove' => '제거',
        'add_item' => '품목 추가',
        'add_step' => '단계 추가',
        'mark_as_read' => '읽음 처리',
        'restore_selected' => '선택 복원',
        'force_delete_selected' => '선택 영구 삭제',
        'match' => '매칭',
        'unmatch' => '매칭 해제',
        'save_profile' => '프로필 저장',
        'change_password' => '비밀번호 변경',
    ],

    /*
    |--------------------------------------------------------------------------
    | 확인/모달 메시지
    |--------------------------------------------------------------------------
    */
    'confirmations' => [
        'delete' => '정말 삭제하시겠습니까?',
        'delete_description' => '이 작업은 되돌릴 수 없습니다.',
        'restore' => '이 항목을 복원하시겠습니까?',
        'restore_heading' => '복원 확인',
        'force_delete' => '이 항목을 영구적으로 삭제합니다. 이 작업은 되돌릴 수 없습니다.',
        'force_delete_heading' => '영구 삭제 확인',
        'restore_selected' => '선택한 항목을 모두 복원하시겠습니까?',
        'restore_selected_heading' => '선택 항목 복원',
        'force_delete_selected' => '선택한 항목을 영구적으로 삭제합니다. 이 작업은 되돌릴 수 없습니다.',
        'force_delete_selected_heading' => '선택 항목 영구 삭제',
        'approve' => '이 항목을 승인하시겠습니까?',
        'reject' => '이 항목을 반려하시겠습니까?',
        'notification_restore' => '이 알림을 복원하시겠습니까?',
        'notification_restore_heading' => '알림 복원',
    ],

    /*
    |--------------------------------------------------------------------------
    | 알림(Notification) 메시지
    |--------------------------------------------------------------------------
    */
    'notifications' => [
        'created' => ':resource이(가) 생성되었습니다.',
        'updated' => ':resource이(가) 업데이트되었습니다.',
        'deleted' => ':resource이(가) 삭제되었습니다.',
        'restored' => '복원 완료',
        'force_deleted' => '영구 삭제 완료',
        'restored_count' => ':count건 복원 완료',
        'force_deleted_count' => ':count건 영구 삭제 완료',
        'approved' => '승인되었습니다.',
        'rejected' => '반려되었습니다.',
        'profile_updated' => '프로필이 업데이트되었습니다.',
        'password_changed' => '비밀번호가 변경되었습니다.',
        'matching_success' => '결제 매칭 완료',
        'matching_failed' => '매칭 실패',
        'matching_already_processed' => '이미 처리됨',
        'matching_already_processed_body' => '이미 처리된 입금 내역입니다.',
        'matching_not_found' => '입금 내역 또는 청구서를 찾을 수 없습니다.',
        'unmatching_success' => '매칭 해제 완료',
        'unmatching_failed' => '해제 실패',
        'unmatching_not_found' => '처리된 입금 내역을 찾을 수 없습니다.',
        'saved' => '저장되었습니다.',
        'error' => '오류가 발생했습니다.',
    ],

    /*
    |--------------------------------------------------------------------------
    | 섹션 제목
    |--------------------------------------------------------------------------
    */
    'sections' => [
        'basic_info' => '기본 정보',
        'additional_info' => '추가 정보',
        'personal_info' => '개인 정보',
        'contact_info' => '연락처',
        'company_info' => '기업 정보',
        'classification' => '분류',
        'schedule' => '일정',
        'budget_and_status' => '예산 및 상태',
        'detail_content' => '상세 내용',
        'note' => '비고',
        'approval' => '승인',
        'approval_info' => '승인 정보',
        'assignment_and_status' => '담당 및 상태',
        'billing_and_status' => '청구 및 상태',
        'additional_settings' => '추가 설정',
        'work_info' => '근무 정보',
        'password_change' => '비밀번호 변경',
        'conditions' => '적용 조건',
        'approval_steps' => '결재 단계',

        // Resource-specific sections
        'sales_info' => '영업 정보',
        'customer_info' => '기업 정보',
        'invoice_info' => '청구서 정보',
        'invoice_item_info' => '청구 항목 정보',
        'contract_info' => '계약 정보',
        'contract_terms' => '계약 조건',
        'contract_status' => '계약 상태',
        'project_info' => '프로젝트 정보',
        'task_info' => '작업 정보',
        'expense_info' => '비용 정보',
        'payment_info' => '결제 정보',
        'order_info' => '주문 정보',
        'order_items' => '주문 품목',
        'timesheet_info' => '타임시트 정보',
        'account_info' => '계정과목 정보',
        'leave_request' => '휴가 신청',
        'leave_info' => '휴가 정보',
        'attendance_info' => '근태 정보',
        'milestone_info' => '마일스톤 정보',
        'stock_info' => '재고 정보',
        'stock_movement_info' => '재고 이동 정보',
        'user_info' => '사용자 정보',
        'employee_info' => '직원 정보',
        'product_category_info' => '상품 카테고리 정보',
        'expense_category_info' => '비용 카테고리 정보',
        'deposit_info' => '입금 정보',
        'approval_flow_info' => '결재라인 정보',
        'department_info' => '부서 정보',
        'leave_type_info' => '휴가 유형 정보',
        'supplier_info' => '공급업체 정보',
        'product_info' => '상품 정보',
        'price_info' => '가격',
        'stock_settings' => '재고 설정',
        'warehouse_info' => '창고 정보',
        'price_stock' => '가격/재고',
        'role_info' => '역할 정보',
    ],

    /*
    |--------------------------------------------------------------------------
    | 빈 상태 메시지
    |--------------------------------------------------------------------------
    */
    'empty_states' => [
        'no_records' => '데이터가 없습니다.',
        'no_deleted_items' => '삭제된 항목 없음',
        'trash_empty' => '휴지통이 비어 있습니다.',
        'no_notifications' => '알림이 없습니다',
        'notifications_description' => '수신된 알림이 여기에 표시됩니다.',
        'no_login_history' => '로그인 기록이 없습니다',
        'login_history_description' => '로그인/로그아웃 기록이 여기에 표시됩니다.',
        'no_results' => '검색 결과가 없습니다.',
    ],

    /*
    |--------------------------------------------------------------------------
    | 검색/필터 라벨
    |--------------------------------------------------------------------------
    */
    'search' => [
        'placeholder' => '검색...',
        'search' => '검색',
        'filter' => '필터',
        'clear_filters' => '필터 초기화',
        'all' => '전체',
        'select' => '선택',
        'no_options' => '옵션이 없습니다.',
        'select_first' => '먼저 선택하세요',
    ],

    /*
    |--------------------------------------------------------------------------
    | 승인 관련
    |--------------------------------------------------------------------------
    */
    'approval' => [
        'pending' => '대기',
        'approved' => '승인',
        'rejected' => '반려',
        'cancelled' => '취소',
        'approval_request' => '승인요청',
        'consensus' => '합의',
        'reference' => '참조',
        'approval_type_approve' => '승인 (승인/반려 가능)',
        'approval_type_consensus' => '합의 (의견 제출, 거부 불가)',
        'approval_type_reference' => '참조 (열람만, 알림 발송)',
        'specific_user' => '특정 사용자',
        'role' => '역할',
    ],

    /*
    |--------------------------------------------------------------------------
    | 네비게이션 그룹
    |--------------------------------------------------------------------------
    */
    'nav_groups' => [
        'crm' => 'CRM',
        'finance' => '재무/회계',
        'project' => '프로젝트',
        'hr' => '인사관리',
        'purchasing' => '구매관리',
        'inventory' => '재고관리',
        'inventory_logistics' => '재고/물류',
        'system' => '시스템설정',
        'my_settings' => '내 설정',
    ],

    /*
    |--------------------------------------------------------------------------
    | 네비게이션 라벨 (리소스)
    |--------------------------------------------------------------------------
    */
    'nav_labels' => [
        'customers' => '고객 관리',
        'contacts' => '연락처',
        'leads' => '잠재 고객 발굴',
        'opportunities' => '영업 기회',
        'contracts' => '계약 관리',
        'payment_matching' => '결제(청구/입금) 관리',
        'invoices' => '청구서',
        'invoice_items' => '청구 항목',
        'payments' => '결제 내역',
        'bank_deposits' => '입금 내역',
        'expenses' => '비용 관리',
        'expense_categories' => '비용 카테고리',
        'accounts' => '계정과목',
        'projects' => '프로젝트 관리',
        'tasks' => '작업',
        'timesheets' => '타임시트',
        'milestones' => '마일스톤',
        'departments' => '부서 관리',
        'employees' => '직원 관리',
        'leaves' => '휴가 관리',
        'leave_types' => '휴가 유형',
        'attendances' => '근태 관리',
        'suppliers' => '공급업체',
        'purchase_orders' => '구매주문',
        'purchase_order_items' => '구매주문 항목',
        'products' => '상품',
        'product_categories' => '상품 카테고리',
        'stocks' => '재고 현황',
        'stock_movements' => '재고 이동',
        'warehouses' => '창고 관리',
        'users' => '사용자 관리',
        'roles' => '역할 관리',
        'approval_flows' => '결재라인 관리',
        'trash' => '휴지통',
        'my_profile' => '프로필 관리',
        'notification_history' => '알림 내역',
    ],

    /*
    |--------------------------------------------------------------------------
    | 모델 라벨 (단수/복수)
    |--------------------------------------------------------------------------
    */
    'models' => [
        'customer' => '고객',
        'contact' => '연락처',
        'lead' => '잠재 고객',
        'opportunity' => '영업 기회',
        'contract' => '계약',
        'invoice' => '청구서',
        'invoice_item' => '청구 항목',
        'payment' => '결제',
        'bank_deposit' => '입금 내역',
        'expense' => '비용',
        'expense_category' => '비용 카테고리',
        'account' => '계정과목',
        'project' => '프로젝트',
        'task' => '작업',
        'timesheet' => '타임시트',
        'milestone' => '마일스톤',
        'department' => '부서',
        'employee' => '직원',
        'leave' => '휴가',
        'leave_type' => '휴가 유형',
        'attendance' => '근태',
        'supplier' => '공급업체',
        'purchase_order' => '구매주문',
        'purchase_order_item' => '구매주문 항목',
        'product' => '상품',
        'product_category' => '상품 카테고리',
        'stock' => '재고',
        'stock_movement' => '재고 이동',
        'warehouse' => '창고',
        'user' => '사용자',
        'role' => '역할',
        'approval_flow' => '결재라인',
    ],

    /*
    |--------------------------------------------------------------------------
    | 상태 라벨 (공통)
    |--------------------------------------------------------------------------
    */
    'statuses' => [
        'active' => '활성',
        'inactive' => '비활성',
        'draft' => '초안',
        'pending' => '대기',
        'in_progress' => '진행중',
        'completed' => '완료',
        'cancelled' => '취소',
        'on_hold' => '보류',
        'approved' => '승인',
        'rejected' => '반려',
        'overdue' => '연체',
        'expired' => '만료됨',
        'valid' => '유효',
        'read' => '읽음',
        'unread' => '안읽음',
        'deleted' => '삭제됨',
        'processed' => '처리 완료',
        'unprocessed' => '미처리',
    ],

    /*
    |--------------------------------------------------------------------------
    | 테이블/목록 관련
    |--------------------------------------------------------------------------
    */
    'table' => [
        'actions' => '작업',
        'bulk_actions' => '일괄 작업',
        'no_records' => '데이터가 없습니다.',
        'showing' => ':total건 중 :first~:last 표시',
        'selected' => ':count건 선택됨',
        'per_page' => '페이지당 :count건',
        'sort_asc' => '오름차순',
        'sort_desc' => '내림차순',
    ],

    /*
    |--------------------------------------------------------------------------
    | 일반 라벨
    |--------------------------------------------------------------------------
    */
    'general' => [
        'yes' => '예',
        'no' => '아니오',
        'none' => '없음',
        'all' => '전체',
        'auto_generated' => '자동 생성',
        'won' => '원',
        'currency_prefix' => '₩',
        'percent' => '%',
        'hours_suffix' => 'h',
        'days_suffix' => '일',
        'months_suffix' => '개월',
        'items_count' => ':count개 품목',
        'login' => '로그인',
        'logout' => '로그아웃',
        'login_failed' => '로그인 실패',
        'korean' => '한국어',
        'english' => 'English',
        'detail' => '상세',
    ],

    /*
    |--------------------------------------------------------------------------
    | 플레이스홀더/도움말 텍스트
    |--------------------------------------------------------------------------
    */
    'placeholders' => [
        'select' => '선택',
        'none' => '없음',
        'none_top_category' => '없음 (최상위 카테고리)',
        'none_top_department' => '없음 (최상위 부서)',
        'auto_generated' => '자동 생성',
        'select_project_first' => '프로젝트를 먼저 선택하세요',
        'select_task' => '작업 선택',
        'example_bank_account' => '예: 우리은행 1005-xxx-xxxx',
        'example_approval_name' => '예: 구매주문 기본 결재',
    ],

    /*
    |--------------------------------------------------------------------------
    | 도움말(Helper) 텍스트
    |--------------------------------------------------------------------------
    */
    'helpers' => [
        'account_for_payment' => '입출금이 처리되는 은행/현금 계정',
        'default_approval_flow' => '해당 대상 유형의 기본 결재라인으로 설정합니다. 조건에 매칭되는 결재라인이 없을 때 사용됩니다.',
        'approval_conditions' => '조건을 설정하면 해당 조건에 맞는 문서에 자동으로 이 결재라인이 적용됩니다. 조건이 없으면 "기본 결재라인"으로만 사용됩니다.',
        'approval_steps' => '결재 단계를 순서대로 추가하세요. 순서는 자동으로 부여됩니다.',
        'sales_account' => '이 카테고리 상품을 판매할 때 적용되는 매출 계정',
        'purchase_account' => '이 카테고리 상품을 구매할 때 적용되는 매입 계정',
        'profile_description' => '이름, 이메일, 프로필 사진을 수정합니다.',
        'password_description' => '비밀번호를 변경합니다. 변경하지 않으려면 비워두세요.',
        'unlimited' => '무제한',
    ],

    /*
    |--------------------------------------------------------------------------
    | 페이지 제목
    |--------------------------------------------------------------------------
    */
    'pages' => [
        'dashboard' => '대시보드',
        'login_ip' => '접속 IP',
        'trash' => '휴지통',
        'payment_matching' => '결제(청구/입금) 관리',
        'my_profile' => '프로필 관리',
        'notification_settings' => '알림 설정',
        'notification_history' => '알림 내역',
    ],

    /*
    |--------------------------------------------------------------------------
    | 대상 유형 (결재라인)
    |--------------------------------------------------------------------------
    */
    'target_types' => [
        'purchase_order' => '구매주문',
        'expense' => '비용',
        'leave' => '휴가',
        'timesheet' => '타임시트',
    ],

    /*
    |--------------------------------------------------------------------------
    | 알림 이벤트 라벨
    |--------------------------------------------------------------------------
    */
    'events' => [
        'login' => '로그인',
        'logout' => '로그아웃',
        'login_failed' => '로그인 실패',
    ],

    /*
    |--------------------------------------------------------------------------
    | 권한 관련
    |--------------------------------------------------------------------------
    */
    'permissions' => [
        'actions' => [
            'view' => '보기',
            'create' => '생성',
            'update' => '수정',
            'delete' => '삭제',
            'approve' => '승인',
            'sign' => '서명',
            'convert' => '전환',
            'export' => '내보내기',
            'adjust' => '조정',
        ],
        'edit_title' => '역할 편집: :name',
        'saved' => '권한이 저장되었습니다.',
    ],

    /*
    |--------------------------------------------------------------------------
    | 위젯 제목
    |--------------------------------------------------------------------------
    */
    'widgets' => [
        'active_projects' => '진행 중인 프로젝트',
        'latest_invoices' => '최근 청구서',
        'monthly_revenue_expense' => '월별 매출/비용 현황',
        'revenue' => '매출',
        'expense' => '비용',
    ],

    /*
    |--------------------------------------------------------------------------
    | 통계 위젯
    |--------------------------------------------------------------------------
    */
    'stats' => [
        'monthly_revenue' => '이번 달 매출',
        'increase' => ':value% 증가',
        'decrease' => ':value% 감소',
        'pending_amount' => '미결제 금액',
        'payment_pending' => '결제 대기 중',
        'active_projects' => '진행 중 프로젝트',
        'active_projects_desc' => '활성 프로젝트',
        'customers_employees' => '고객 / 직원',
        'customers_employees_value' => ':customers개사 / :employees명',
        'active_customers_employees' => '활성 고객 / 재직 직원',
        'paid_basis' => '결제완료 기준',
        'monthly_expense' => '이번 달 비용',
        'approved_expense' => '승인된 비용',
        'pending_approval_expense' => '승인 대기 비용',
        'count_suffix' => ':count건',
        'needs_review' => '검토 필요',
        'active_employees' => '재직 직원',
        'person_count' => ':count명',
        'currently_active' => '현재 재직 중',
        'today_attendance' => '오늘 출근',
        'today_attendance_desc' => '금일 출근 현황',
        'pending_leave' => '휴가 승인 대기',
        'new_hires_month' => '이번 달 신규 입사',
        'year_month_format' => ':year년 :month월',
        'my_tasks' => '내 작업',
        'in_progress_tasks' => '진행 중인 작업',
        'participating_projects' => '참여 프로젝트',
        'in_progress' => '진행 중',
        'remaining_leave' => '남은 연차',
        'day_count' => ':count일',
        'year_basis' => ':year년 기준',
        'pending_expenses' => '비용 처리 대기',
        'submitted_expenses' => '제출한 비용',
        'projects_count' => ':count개',
    ],

    /*
    |--------------------------------------------------------------------------
    | 활동 로그 패널
    |--------------------------------------------------------------------------
    */
    'activity_log' => [
        'title' => '활동 로그',
        'no_activity' => '기록된 활동이 없습니다.',
        'created' => '생성',
        'updated' => '수정',
        'deleted' => '삭제',
        'system' => '시스템',
        'and_more' => '외 :count건',
    ],

    /*
    |--------------------------------------------------------------------------
    | 결재 현황 패널
    |--------------------------------------------------------------------------
    */
    'approval_panel' => [
        'title' => '결재 진행 현황',
        'approval_line' => '결재라인',
        'in_progress' => '진행중',
        'step_progress' => ':current/:total단계',
        'final_approved' => '최종 승인',
        'rejected' => '반려',
        'requester' => '신청자',
        'request_date' => '신청일',
        'completed_date' => '완료일',
        'step_n' => ':n단계',
        'skip' => '스킵',
        'waiting' => '대기중',
        'upcoming' => '예정',
        'role_label' => '(역할)',
        'auto_skip_reason' => '신청자가 해당 단계 권한 보유 (자동 스킵)',
    ],

    /*
    |--------------------------------------------------------------------------
    | 결제 매칭 페이지
    |--------------------------------------------------------------------------
    */
    'payment_matching' => [
        'invoice_list' => '청구서 목록',
        'deposit_list' => '입금 내역',
        'unpaid_partial' => '미결제/부분결제',
        'unprocessed' => '미처리',
        'processed' => '처리 완료',
        'invoice_number_col' => '청구번호',
        'invoice_amount' => '청구 금액',
        'processing_col' => '처리',
        'deposit_date' => '입금일시',
        'depositor' => '입금자',
        'no_invoices' => '청구서가 없습니다.',
        'no_deposits' => '입금 내역이 없습니다.',
        'confirm_title' => '결제 매칭 확인',
        'confirm_depositor' => '입금자:',
        'confirm_amount' => '금액:',
        'confirm_invoice' => '청구서:',
        'confirm_message' => '이 입금 내역을 해당 청구서의 결제로 등록하시겠습니까?',
        'register_payment' => '결제 등록',
        'unmatch_confirm' => '이 입금 내역의 결제 매칭을 해제하시겠습니까?',
        'drag_hint' => '좌측 청구서로 드래그하세요',
        'deposit_matching_note' => '입금 내역 매칭: :name',
        'matching_success_body' => ':name ₩:amount → :invoice',
        'unmatching_success_body' => ':name의 결제 매칭이 해제되었습니다.',
    ],

    /*
    |--------------------------------------------------------------------------
    | 휴지통 페이지
    |--------------------------------------------------------------------------
    */
    'trash_page' => [
        'type_col' => '유형',
        'name_col' => '이름',
        'detail_1' => '상세 1',
        'detail_2' => '상세 2',
    ],

    /*
    |--------------------------------------------------------------------------
    | 보기 모드 토글
    |--------------------------------------------------------------------------
    */
    'view_mode' => [
        'page' => '페이지',
        'slide' => '슬라이드',
    ],

    /*
    |--------------------------------------------------------------------------
    | 네비게이션 순서 (사이드바)
    |--------------------------------------------------------------------------
    */
    'nav_order' => [
        'change_order' => '순서 변경',
        'apply_order' => '순서 적용',
        'reset_order' => '순서 초기화',
    ],

    /*
    |--------------------------------------------------------------------------
    | 알림 설정 페이지
    |--------------------------------------------------------------------------
    */
    'notification_settings' => [
        'saved' => '알림 설정이 저장되었습니다.',
        'category_work' => '업무',
        'category_approval' => '승인/결재',
        'category_crm' => 'CRM',
        'category_finance' => '재무/재고',
        'task_assigned_label' => '태스크 배정',
        'task_assigned_desc' => '태스크가 나에게 배정되었을 때',
        'task_status_changed_label' => '태스크 완료',
        'task_status_changed_desc' => '담당 프로젝트의 태스크가 완료되었을 때',
        'milestone_completed_label' => '마일스톤 완료',
        'milestone_completed_desc' => '담당 프로젝트의 마일스톤이 완료되었을 때',
        'leave_requested_label' => '휴가 신청',
        'leave_requested_desc' => '새로운 휴가 신청이 접수되었을 때',
        'leave_status_changed_label' => '휴가 승인/반려',
        'leave_status_changed_desc' => '신청한 휴가가 승인 또는 반려되었을 때',
        'expense_submitted_label' => '비용 청구',
        'expense_submitted_desc' => '새로운 비용 청구 승인 요청이 접수되었을 때',
        'expense_status_changed_label' => '비용 승인/반려',
        'expense_status_changed_desc' => '청구한 비용이 승인 또는 반려되었을 때',
        'purchase_order_approval_label' => '구매주문 승인',
        'purchase_order_approval_desc' => '새로운 구매주문 승인 요청이 접수되었을 때',
        'lead_assigned_label' => '리드 배정',
        'lead_assigned_desc' => '새 리드가 나에게 배정되었을 때',
        'opportunity_stage_changed_label' => '영업기회 단계 변경',
        'opportunity_stage_changed_desc' => '담당 영업기회의 단계가 변경되었을 때',
        'invoice_overdue_label' => '송장 연체',
        'invoice_overdue_desc' => '송장 결제 기한이 초과되었을 때',
        'contract_expiring_label' => '계약 만료 임박',
        'contract_expiring_desc' => '계약 만료일이 가까울 때',
        'low_stock_label' => '재고 부족',
        'low_stock_desc' => '상품 재고가 최소 수량 이하일 때',
        'payment_received_label' => '결제 수신',
        'payment_received_desc' => '담당 송장에 결제가 입금되었을 때',
    ],

];
