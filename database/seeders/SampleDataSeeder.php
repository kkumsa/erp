<?php

namespace Database\Seeders;

use App\Models\Contact;
use App\Models\Contract;
use App\Models\Customer;
use App\Models\Department;
use App\Models\Employee;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Milestone;
use App\Models\Payment;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\Project;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\Stock;
use App\Models\StockMovement;
use App\Models\Supplier;
use App\Models\Task;
use App\Models\Timesheet;
use App\Models\User;
use App\Models\Warehouse;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SampleDataSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('IT 스타트업 샘플 데이터 생성 중...');

        // 1. 부서 생성
        $departments = $this->createDepartments();
        $this->command->info('✓ 부서 생성 완료');

        // 2. 사용자 및 직원 생성
        $users = $this->createUsersAndEmployees($departments);
        $this->command->info('✓ 사용자/직원 생성 완료');

        // 3. 창고 생성
        $warehouses = $this->createWarehouses($users);
        $this->command->info('✓ 창고 생성 완료');

        // 4. 상품 카테고리 및 상품 생성
        $products = $this->createProductsAndCategories();
        $this->command->info('✓ 상품 생성 완료');

        // 5. 공급업체 생성
        $suppliers = $this->createSuppliers();
        $this->command->info('✓ 공급업체 생성 완료');

        // 6. 고객사 및 담당자 생성
        $customers = $this->createCustomersAndContacts($users);
        $this->command->info('✓ 고객사 생성 완료');

        // 7. 계약 생성
        $contracts = $this->createContracts($customers, $users);
        $this->command->info('✓ 계약 생성 완료');

        // 8. 프로젝트 생성
        $projects = $this->createProjects($customers, $contracts, $users);
        $this->command->info('✓ 프로젝트 생성 완료');

        // 9. 마일스톤 및 태스크 생성
        $this->createMilestonesAndTasks($projects, $users);
        $this->command->info('✓ 마일스톤/태스크 생성 완료');

        // 10. 타임시트 생성
        $this->createTimesheets($projects, $users);
        $this->command->info('✓ 타임시트 생성 완료');

        // 11. 발주서 생성
        $this->createPurchaseOrders($suppliers, $products, $warehouses, $users);
        $this->command->info('✓ 발주서 생성 완료');

        // 12. 청구서 및 결제 생성
        $this->createInvoicesAndPayments($customers, $contracts, $projects, $products, $users);
        $this->command->info('✓ 청구서/결제 생성 완료');

        // 13. 비용 생성
        $this->createExpenses($users, $projects, $suppliers);
        $this->command->info('✓ 비용 생성 완료');

        $this->command->info('');
        $this->command->info('🎉 IT 스타트업 샘플 데이터 생성 완료!');
        $this->command->info('');
        $this->command->info('관리자 계정:');
        $this->command->info('  이메일: admin@techwave.kr');
        $this->command->info('  비밀번호: password');
    }

    private function createDepartments(): array
    {
        $departments = [];

        // 본부
        $headquarters = Department::create([
            'name' => '경영본부',
            'code' => 'HQ',
            'description' => '회사 전체 경영 총괄',
            'is_active' => true,
            'sort_order' => 1,
        ]);
        $departments['headquarters'] = $headquarters;

        // 경영지원팀
        $departments['admin'] = Department::create([
            'name' => '경영지원팀',
            'code' => 'ADM',
            'description' => '인사, 총무, 재무 업무',
            'parent_id' => $headquarters->id,
            'is_active' => true,
            'sort_order' => 2,
        ]);

        // 개발본부
        $devHq = Department::create([
            'name' => '개발본부',
            'code' => 'DEV-HQ',
            'description' => '기술 개발 총괄',
            'is_active' => true,
            'sort_order' => 3,
        ]);
        $departments['dev_hq'] = $devHq;

        // 백엔드팀
        $departments['backend'] = Department::create([
            'name' => '백엔드개발팀',
            'code' => 'BE',
            'description' => '서버, API, 데이터베이스 개발',
            'parent_id' => $devHq->id,
            'is_active' => true,
            'sort_order' => 4,
        ]);

        // 프론트엔드팀
        $departments['frontend'] = Department::create([
            'name' => '프론트엔드개발팀',
            'code' => 'FE',
            'description' => '웹, 앱 프론트엔드 개발',
            'parent_id' => $devHq->id,
            'is_active' => true,
            'sort_order' => 5,
        ]);

        // 기획팀
        $departments['planning'] = Department::create([
            'name' => '서비스기획팀',
            'code' => 'PLN',
            'description' => '서비스 기획 및 PM',
            'is_active' => true,
            'sort_order' => 6,
        ]);

        // 디자인팀
        $departments['design'] = Department::create([
            'name' => '디자인팀',
            'code' => 'DSN',
            'description' => 'UI/UX 디자인',
            'is_active' => true,
            'sort_order' => 7,
        ]);

        // 영업팀
        $departments['sales'] = Department::create([
            'name' => '영업팀',
            'code' => 'SLS',
            'description' => '영업 및 고객 관리',
            'is_active' => true,
            'sort_order' => 8,
        ]);

        return $departments;
    }

    private function createUsersAndEmployees(array $departments): array
    {
        $users = [];
        $baseDate = now()->subMonth();

        // CEO
        $ceo = User::create([
            'name' => '김대표',
            'email' => 'ceo@techwave.kr',
            'password' => Hash::make('password'),
            'is_active' => true,
        ]);
        $ceo->assignRole('Super Admin');
        Employee::create([
            'user_id' => $ceo->id,
            'department_id' => $departments['headquarters']->id,
            'employee_code' => 'TW-001',
            'position' => '임원',
            'job_title' => '대표이사',
            'hire_date' => $baseDate->copy()->subYears(2),
            'birth_date' => '1980-03-15',
            'phone' => '010-1234-0001',
            'employment_type' => '정규직',
            'status' => '재직',
            'base_salary' => 15000000,
            'annual_leave_days' => 25,
        ]);
        $users['ceo'] = $ceo;

        // 관리자 (CTO)
        $admin = User::create([
            'name' => '박기술',
            'email' => 'admin@techwave.kr',
            'password' => Hash::make('password'),
            'is_active' => true,
        ]);
        $admin->assignRole('Super Admin');
        Employee::create([
            'user_id' => $admin->id,
            'department_id' => $departments['dev_hq']->id,
            'employee_code' => 'TW-002',
            'position' => '임원',
            'job_title' => 'CTO',
            'hire_date' => $baseDate->copy()->subYears(2),
            'birth_date' => '1985-07-22',
            'phone' => '010-1234-0002',
            'employment_type' => '정규직',
            'status' => '재직',
            'base_salary' => 12000000,
            'annual_leave_days' => 25,
        ]);
        $users['admin'] = $admin;

        // 부서장 설정
        $departments['headquarters']->update(['manager_id' => $ceo->id]);
        $departments['dev_hq']->update(['manager_id' => $admin->id]);

        // HR 매니저
        $hrManager = User::create([
            'name' => '이인사',
            'email' => 'hr@techwave.kr',
            'password' => Hash::make('password'),
            'is_active' => true,
        ]);
        $hrManager->assignRole('HR Manager');
        Employee::create([
            'user_id' => $hrManager->id,
            'department_id' => $departments['admin']->id,
            'employee_code' => 'TW-003',
            'position' => '팀장',
            'job_title' => '인사팀장',
            'hire_date' => $baseDate->copy()->subYear(),
            'birth_date' => '1988-11-05',
            'phone' => '010-1234-0003',
            'employment_type' => '정규직',
            'status' => '재직',
            'base_salary' => 6500000,
            'annual_leave_days' => 18,
        ]);
        $departments['admin']->update(['manager_id' => $hrManager->id]);
        $users['hr'] = $hrManager;

        // 회계 담당자
        $accountant = User::create([
            'name' => '정회계',
            'email' => 'finance@techwave.kr',
            'password' => Hash::make('password'),
            'is_active' => true,
        ]);
        $accountant->assignRole('Accountant');
        Employee::create([
            'user_id' => $accountant->id,
            'department_id' => $departments['admin']->id,
            'employee_code' => 'TW-004',
            'position' => '매니저',
            'job_title' => '재무매니저',
            'hire_date' => $baseDate->copy()->subMonths(8),
            'birth_date' => '1990-02-14',
            'phone' => '010-1234-0004',
            'employment_type' => '정규직',
            'status' => '재직',
            'base_salary' => 5500000,
            'annual_leave_days' => 15,
        ]);
        $users['accountant'] = $accountant;

        // 백엔드 개발자들
        $backendLead = User::create([
            'name' => '최백엔',
            'email' => 'backend.lead@techwave.kr',
            'password' => Hash::make('password'),
            'is_active' => true,
        ]);
        $backendLead->assignRole('Manager');
        Employee::create([
            'user_id' => $backendLead->id,
            'department_id' => $departments['backend']->id,
            'employee_code' => 'TW-005',
            'position' => '팀장',
            'job_title' => '백엔드 리드',
            'hire_date' => $baseDate->copy()->subMonths(10),
            'birth_date' => '1991-06-18',
            'phone' => '010-1234-0005',
            'employment_type' => '정규직',
            'status' => '재직',
            'base_salary' => 7000000,
            'annual_leave_days' => 15,
        ]);
        $departments['backend']->update(['manager_id' => $backendLead->id]);
        $users['backend_lead'] = $backendLead;

        $backendDev1 = User::create([
            'name' => '강서버',
            'email' => 'dev1@techwave.kr',
            'password' => Hash::make('password'),
            'is_active' => true,
        ]);
        $backendDev1->assignRole('Employee');
        Employee::create([
            'user_id' => $backendDev1->id,
            'department_id' => $departments['backend']->id,
            'employee_code' => 'TW-006',
            'position' => '사원',
            'job_title' => '백엔드 개발자',
            'hire_date' => $baseDate->copy()->subMonths(6),
            'birth_date' => '1995-09-23',
            'phone' => '010-1234-0006',
            'employment_type' => '정규직',
            'status' => '재직',
            'base_salary' => 4500000,
            'annual_leave_days' => 15,
        ]);
        $users['backend_dev1'] = $backendDev1;

        $backendDev2 = User::create([
            'name' => '윤데이터',
            'email' => 'dev2@techwave.kr',
            'password' => Hash::make('password'),
            'is_active' => true,
        ]);
        $backendDev2->assignRole('Employee');
        Employee::create([
            'user_id' => $backendDev2->id,
            'department_id' => $departments['backend']->id,
            'employee_code' => 'TW-007',
            'position' => '대리',
            'job_title' => '백엔드 개발자',
            'hire_date' => $baseDate->copy()->subMonths(4),
            'birth_date' => '1993-12-01',
            'phone' => '010-1234-0007',
            'employment_type' => '정규직',
            'status' => '재직',
            'base_salary' => 5000000,
            'annual_leave_days' => 15,
        ]);
        $users['backend_dev2'] = $backendDev2;

        // 프론트엔드 개발자들
        $frontendLead = User::create([
            'name' => '한프론',
            'email' => 'frontend.lead@techwave.kr',
            'password' => Hash::make('password'),
            'is_active' => true,
        ]);
        $frontendLead->assignRole('Manager');
        Employee::create([
            'user_id' => $frontendLead->id,
            'department_id' => $departments['frontend']->id,
            'employee_code' => 'TW-008',
            'position' => '팀장',
            'job_title' => '프론트엔드 리드',
            'hire_date' => $baseDate->copy()->subMonths(9),
            'birth_date' => '1992-04-10',
            'phone' => '010-1234-0008',
            'employment_type' => '정규직',
            'status' => '재직',
            'base_salary' => 6500000,
            'annual_leave_days' => 15,
        ]);
        $departments['frontend']->update(['manager_id' => $frontendLead->id]);
        $users['frontend_lead'] = $frontendLead;

        $frontendDev = User::create([
            'name' => '오리액',
            'email' => 'dev3@techwave.kr',
            'password' => Hash::make('password'),
            'is_active' => true,
        ]);
        $frontendDev->assignRole('Employee');
        Employee::create([
            'user_id' => $frontendDev->id,
            'department_id' => $departments['frontend']->id,
            'employee_code' => 'TW-009',
            'position' => '사원',
            'job_title' => '프론트엔드 개발자',
            'hire_date' => $baseDate->copy()->subMonths(3),
            'birth_date' => '1996-08-25',
            'phone' => '010-1234-0009',
            'employment_type' => '정규직',
            'status' => '재직',
            'base_salary' => 4200000,
            'annual_leave_days' => 15,
        ]);
        $users['frontend_dev'] = $frontendDev;

        // 기획팀
        $planner = User::create([
            'name' => '서기획',
            'email' => 'planner@techwave.kr',
            'password' => Hash::make('password'),
            'is_active' => true,
        ]);
        $planner->assignRole('Manager');
        Employee::create([
            'user_id' => $planner->id,
            'department_id' => $departments['planning']->id,
            'employee_code' => 'TW-010',
            'position' => '팀장',
            'job_title' => '서비스 기획팀장',
            'hire_date' => $baseDate->copy()->subMonths(11),
            'birth_date' => '1989-05-30',
            'phone' => '010-1234-0010',
            'employment_type' => '정규직',
            'status' => '재직',
            'base_salary' => 6000000,
            'annual_leave_days' => 15,
        ]);
        $departments['planning']->update(['manager_id' => $planner->id]);
        $users['planner'] = $planner;

        // 디자이너
        $designer = User::create([
            'name' => '문디자',
            'email' => 'designer@techwave.kr',
            'password' => Hash::make('password'),
            'is_active' => true,
        ]);
        $designer->assignRole('Employee');
        Employee::create([
            'user_id' => $designer->id,
            'department_id' => $departments['design']->id,
            'employee_code' => 'TW-011',
            'position' => '팀장',
            'job_title' => 'UI/UX 디자이너',
            'hire_date' => $baseDate->copy()->subMonths(7),
            'birth_date' => '1994-01-17',
            'phone' => '010-1234-0011',
            'employment_type' => '정규직',
            'status' => '재직',
            'base_salary' => 5500000,
            'annual_leave_days' => 15,
        ]);
        $departments['design']->update(['manager_id' => $designer->id]);
        $users['designer'] = $designer;

        // 영업 담당자
        $salesManager = User::create([
            'name' => '조영업',
            'email' => 'sales@techwave.kr',
            'password' => Hash::make('password'),
            'is_active' => true,
        ]);
        $salesManager->assignRole('Manager');
        Employee::create([
            'user_id' => $salesManager->id,
            'department_id' => $departments['sales']->id,
            'employee_code' => 'TW-012',
            'position' => '팀장',
            'job_title' => '영업팀장',
            'hire_date' => $baseDate->copy()->subMonths(8),
            'birth_date' => '1987-10-08',
            'phone' => '010-1234-0012',
            'employment_type' => '정규직',
            'status' => '재직',
            'base_salary' => 6000000,
            'annual_leave_days' => 15,
        ]);
        $departments['sales']->update(['manager_id' => $salesManager->id]);
        $users['sales'] = $salesManager;

        return $users;
    }

    private function createWarehouses(array $users): array
    {
        $warehouses = [];

        $warehouses['main'] = Warehouse::create([
            'name' => '본사 창고',
            'code' => 'WH-MAIN',
            'address' => '서울시 강남구 테헤란로 123',
            'phone' => '02-1234-5678',
            'manager_id' => $users['hr']->id,
            'is_active' => true,
            'is_default' => true,
            'note' => '본사 사무실 내 물품 보관 창고',
        ]);

        return $warehouses;
    }

    private function createProductsAndCategories(): array
    {
        $products = [];

        // 카테고리 생성
        $devService = ProductCategory::create([
            'name' => '개발 서비스',
            'code' => 'SVC-DEV',
            'description' => '소프트웨어 개발 관련 서비스',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $consulting = ProductCategory::create([
            'name' => '컨설팅',
            'code' => 'SVC-CON',
            'description' => 'IT 컨설팅 서비스',
            'is_active' => true,
            'sort_order' => 2,
        ]);

        $maintenance = ProductCategory::create([
            'name' => '유지보수',
            'code' => 'SVC-MNT',
            'description' => '시스템 유지보수 서비스',
            'is_active' => true,
            'sort_order' => 3,
        ]);

        $equipment = ProductCategory::create([
            'name' => 'IT 장비',
            'code' => 'EQP',
            'description' => 'IT 하드웨어 장비',
            'is_active' => true,
            'sort_order' => 4,
        ]);

        $supplies = ProductCategory::create([
            'name' => '사무용품',
            'code' => 'SUP',
            'description' => '일반 사무용품',
            'is_active' => true,
            'sort_order' => 5,
        ]);

        // 서비스 상품 생성
        $products['web_dev'] = Product::create([
            'code' => 'SVC-001',
            'name' => '웹 애플리케이션 개발',
            'category_id' => $devService->id,
            'description' => '맞춤형 웹 애플리케이션 개발 서비스',
            'unit' => '건',
            'purchase_price' => 0,
            'selling_price' => 50000000,
            'is_active' => true,
            'is_stockable' => false,
        ]);

        $products['mobile_dev'] = Product::create([
            'code' => 'SVC-002',
            'name' => '모바일 앱 개발',
            'category_id' => $devService->id,
            'description' => 'iOS/Android 앱 개발 서비스',
            'unit' => '건',
            'purchase_price' => 0,
            'selling_price' => 40000000,
            'is_active' => true,
            'is_stockable' => false,
        ]);

        $products['api_dev'] = Product::create([
            'code' => 'SVC-003',
            'name' => 'API 개발',
            'category_id' => $devService->id,
            'description' => 'REST API 개발 서비스',
            'unit' => '건',
            'purchase_price' => 0,
            'selling_price' => 20000000,
            'is_active' => true,
            'is_stockable' => false,
        ]);

        $products['it_consulting'] = Product::create([
            'code' => 'CON-001',
            'name' => 'IT 전략 컨설팅',
            'category_id' => $consulting->id,
            'description' => 'IT 전략 수립 및 컨설팅',
            'unit' => '시간',
            'purchase_price' => 0,
            'selling_price' => 300000,
            'is_active' => true,
            'is_stockable' => false,
        ]);

        $products['monthly_maintenance'] = Product::create([
            'code' => 'MNT-001',
            'name' => '월간 유지보수',
            'category_id' => $maintenance->id,
            'description' => '시스템 월간 유지보수 서비스',
            'unit' => '월',
            'purchase_price' => 0,
            'selling_price' => 3000000,
            'is_active' => true,
            'is_stockable' => false,
        ]);

        // IT 장비 (재고 관리 대상)
        $products['laptop'] = Product::create([
            'code' => 'EQP-001',
            'name' => '노트북 (개발용)',
            'category_id' => $equipment->id,
            'description' => 'MacBook Pro 14인치',
            'unit' => '대',
            'purchase_price' => 2800000,
            'selling_price' => 0,
            'min_stock' => 2,
            'max_stock' => 10,
            'is_active' => true,
            'is_stockable' => true,
        ]);

        $products['monitor'] = Product::create([
            'code' => 'EQP-002',
            'name' => '모니터 27인치',
            'category_id' => $equipment->id,
            'description' => 'Dell UltraSharp 27인치 4K',
            'unit' => '대',
            'purchase_price' => 650000,
            'selling_price' => 0,
            'min_stock' => 3,
            'max_stock' => 15,
            'is_active' => true,
            'is_stockable' => true,
        ]);

        $products['keyboard'] = Product::create([
            'code' => 'EQP-003',
            'name' => '기계식 키보드',
            'category_id' => $equipment->id,
            'description' => '레오폴드 FC660M',
            'unit' => '개',
            'purchase_price' => 150000,
            'selling_price' => 0,
            'min_stock' => 5,
            'max_stock' => 20,
            'is_active' => true,
            'is_stockable' => true,
        ]);

        // 사무용품
        $products['paper'] = Product::create([
            'code' => 'SUP-001',
            'name' => 'A4 복사용지',
            'category_id' => $supplies->id,
            'description' => 'A4 복사용지 (박스/2,500매)',
            'unit' => '박스',
            'purchase_price' => 25000,
            'selling_price' => 0,
            'min_stock' => 5,
            'max_stock' => 30,
            'is_active' => true,
            'is_stockable' => true,
        ]);

        $products['pen'] = Product::create([
            'code' => 'SUP-002',
            'name' => '볼펜 (12개입)',
            'category_id' => $supplies->id,
            'description' => '모나미 볼펜 12개입',
            'unit' => '세트',
            'purchase_price' => 8000,
            'selling_price' => 0,
            'min_stock' => 10,
            'max_stock' => 50,
            'is_active' => true,
            'is_stockable' => true,
        ]);

        return $products;
    }

    private function createSuppliers(): array
    {
        $suppliers = [];

        $suppliers['it_equipment'] = Supplier::create([
            'company_name' => '(주)테크마트',
            'code' => 'SUP-001',
            'business_number' => '123-45-67890',
            'representative' => '김테크',
            'contact_name' => '박영업',
            'phone' => '02-555-1234',
            'email' => 'sales@techmart.co.kr',
            'address' => '서울시 용산구 전자상가로 100',
            'bank_name' => '국민은행',
            'bank_account' => '123-456-789012',
            'bank_holder' => '(주)테크마트',
            'status' => '활성',
            'payment_terms' => '정산',
            'payment_days' => 30,
            'note' => 'IT 장비 주거래 업체',
        ]);

        $suppliers['office'] = Supplier::create([
            'company_name' => '오피스디포 코리아',
            'code' => 'SUP-002',
            'business_number' => '234-56-78901',
            'representative' => '이오피',
            'contact_name' => '최담당',
            'phone' => '1588-1234',
            'email' => 'order@officedepot.kr',
            'address' => '서울시 성동구 성수동 234-5',
            'bank_name' => '신한은행',
            'bank_account' => '110-234-567890',
            'bank_holder' => '오피스디포 코리아',
            'status' => '활성',
            'payment_terms' => '선불',
            'payment_days' => 0,
            'note' => '사무용품 공급업체',
        ]);

        $suppliers['cloud'] = Supplier::create([
            'company_name' => '클라우드서비스(주)',
            'code' => 'SUP-003',
            'business_number' => '345-67-89012',
            'representative' => '정클라',
            'contact_name' => '한서버',
            'phone' => '02-777-8888',
            'email' => 'support@cloudservice.kr',
            'address' => '서울시 강남구 역삼동 789',
            'bank_name' => '우리은행',
            'bank_account' => '1002-345-678901',
            'bank_holder' => '클라우드서비스(주)',
            'status' => '활성',
            'payment_terms' => '후불',
            'payment_days' => 15,
            'note' => 'AWS, GCP 파트너사',
        ]);

        return $suppliers;
    }

    private function createCustomersAndContacts(array $users): array
    {
        $customers = [];

        // 고객사 1 - 대기업
        $customers['nexon'] = Customer::create([
            'company_name' => '(주)넥스트게임즈',
            'business_number' => '111-22-33333',
            'representative' => '김게임',
            'industry' => 'IT/게임',
            'business_type' => '게임 개발 및 퍼블리싱',
            'phone' => '02-1111-2222',
            'email' => 'info@nextgames.kr',
            'website' => 'https://www.nextgames.kr',
            'address' => '서울시 강남구 삼성동 159',
            'type' => 'VIP',
            'status' => '활성',
            'assigned_to' => $users['sales']->id,
            'note' => '게임 백오피스 시스템 개발 프로젝트 진행 중',
        ]);

        Contact::create([
            'customer_id' => $customers['nexon']->id,
            'name' => '이개발',
            'position' => '이사',
            'department' => '개발본부',
            'phone' => '02-1111-2223',
            'mobile' => '010-2222-3333',
            'email' => 'dev.lee@nextgames.kr',
            'is_primary' => true,
        ]);

        Contact::create([
            'customer_id' => $customers['nexon']->id,
            'name' => '박구매',
            'position' => '과장',
            'department' => '구매팀',
            'phone' => '02-1111-2224',
            'mobile' => '010-3333-4444',
            'email' => 'purchase@nextgames.kr',
            'is_primary' => false,
        ]);

        // 고객사 2 - 중견기업
        $customers['fintech'] = Customer::create([
            'company_name' => '핀테크솔루션(주)',
            'business_number' => '222-33-44444',
            'representative' => '최핀테',
            'industry' => 'IT/금융',
            'business_type' => '핀테크 솔루션 개발',
            'phone' => '02-3333-4444',
            'email' => 'contact@fintechsol.kr',
            'website' => 'https://www.fintechsol.kr',
            'address' => '서울시 영등포구 여의도동 35',
            'type' => '고객',
            'status' => '활성',
            'assigned_to' => $users['sales']->id,
            'note' => '결제 시스템 API 연동 프로젝트',
        ]);

        Contact::create([
            'customer_id' => $customers['fintech']->id,
            'name' => '정시스템',
            'position' => '팀장',
            'department' => 'IT팀',
            'phone' => '02-3333-4445',
            'mobile' => '010-4444-5555',
            'email' => 'system@fintechsol.kr',
            'is_primary' => true,
        ]);

        // 고객사 3 - 스타트업
        $customers['startup'] = Customer::create([
            'company_name' => '(주)헬스케어랩',
            'business_number' => '333-44-55555',
            'representative' => '오헬스',
            'industry' => 'IT/헬스케어',
            'business_type' => '디지털 헬스케어 플랫폼',
            'phone' => '02-5555-6666',
            'email' => 'hello@healthcarelab.kr',
            'website' => 'https://www.healthcarelab.kr',
            'address' => '서울시 성수동 IT테라스 3층',
            'type' => '고객',
            'status' => '활성',
            'assigned_to' => $users['sales']->id,
            'note' => '헬스케어 앱 MVP 개발',
        ]);

        Contact::create([
            'customer_id' => $customers['startup']->id,
            'name' => '강대표',
            'position' => '대표',
            'department' => '경영',
            'phone' => '02-5555-6667',
            'mobile' => '010-6666-7777',
            'email' => 'ceo@healthcarelab.kr',
            'is_primary' => true,
        ]);

        // 고객사 4 - 유지보수 고객
        $customers['ecommerce'] = Customer::create([
            'company_name' => '쇼핑몰플러스(주)',
            'business_number' => '444-55-66666',
            'representative' => '한쇼핑',
            'industry' => '이커머스',
            'business_type' => '온라인 쇼핑몰 운영',
            'phone' => '02-7777-8888',
            'email' => 'admin@shoppingplus.kr',
            'website' => 'https://www.shoppingplus.kr',
            'address' => '서울시 마포구 상암동 1234',
            'type' => 'VIP',
            'status' => '활성',
            'assigned_to' => $users['sales']->id,
            'note' => '쇼핑몰 시스템 유지보수 계약',
        ]);

        Contact::create([
            'customer_id' => $customers['ecommerce']->id,
            'name' => '윤운영',
            'position' => '매니저',
            'department' => '운영팀',
            'phone' => '02-7777-8889',
            'mobile' => '010-8888-9999',
            'email' => 'operation@shoppingplus.kr',
            'is_primary' => true,
        ]);

        return $customers;
    }

    private function createContracts(array $customers, array $users): array
    {
        $contracts = [];
        $baseDate = now();

        // 계약 1 - 넥스트게임즈 웹 개발
        $contracts['nexon'] = Contract::create([
            'contract_number' => 'CT-' . $baseDate->copy()->subDays(25)->format('Ymd') . '-0001',
            'title' => '게임 백오피스 시스템 개발',
            'customer_id' => $customers['nexon']->id,
            'start_date' => $baseDate->copy()->subDays(20),
            'end_date' => $baseDate->copy()->addMonths(3),
            'amount' => 80000000,
            'status' => '진행중',
            'payment_terms' => '분할',
            'description' => '게임 운영을 위한 백오피스 관리 시스템 개발 (착수금 30%, 중도금 40%, 잔금 30%)',
            'signed_by' => $users['ceo']->id,
            'signed_at' => $baseDate->copy()->subDays(25),
        ]);

        // 계약 2 - 핀테크솔루션 API 개발
        $contracts['fintech'] = Contract::create([
            'contract_number' => 'CT-' . $baseDate->copy()->subDays(15)->format('Ymd') . '-0001',
            'title' => '결제 API 연동 개발',
            'customer_id' => $customers['fintech']->id,
            'start_date' => $baseDate->copy()->subDays(10),
            'end_date' => $baseDate->copy()->addMonths(2),
            'amount' => 35000000,
            'status' => '진행중',
            'payment_terms' => '분할',
            'description' => 'PG사 결제 API 연동 및 커스터마이징 (착수금 50%, 완료 후 50%)',
            'signed_by' => $users['ceo']->id,
            'signed_at' => $baseDate->copy()->subDays(15),
        ]);

        // 계약 3 - 헬스케어랩 앱 개발
        $contracts['startup'] = Contract::create([
            'contract_number' => 'CT-' . $baseDate->copy()->subDays(5)->format('Ymd') . '-0001',
            'title' => '헬스케어 모바일 앱 MVP 개발',
            'customer_id' => $customers['startup']->id,
            'start_date' => $baseDate->copy(),
            'end_date' => $baseDate->copy()->addMonths(2),
            'amount' => 45000000,
            'status' => '진행중',
            'payment_terms' => '후불',
            'description' => '헬스케어 관련 모바일 앱 MVP 버전 개발 (월별 청구)',
            'signed_by' => $users['ceo']->id,
            'signed_at' => $baseDate->copy()->subDays(5),
        ]);

        // 계약 4 - 쇼핑몰플러스 유지보수
        $contracts['ecommerce'] = Contract::create([
            'contract_number' => 'CT-' . $baseDate->copy()->subMonths(1)->format('Ymd') . '-0001',
            'title' => '쇼핑몰 시스템 연간 유지보수',
            'customer_id' => $customers['ecommerce']->id,
            'start_date' => $baseDate->copy()->subMonths(1),
            'end_date' => $baseDate->copy()->addMonths(11),
            'amount' => 36000000,
            'status' => '진행중',
            'payment_terms' => '후불',
            'description' => '쇼핑몰 시스템 유지보수 및 기술 지원 (월 300만원 정기청구)',
            'signed_by' => $users['ceo']->id,
            'signed_at' => $baseDate->copy()->subMonths(1),
        ]);

        return $contracts;
    }

    private function createProjects(array $customers, array $contracts, array $users): array
    {
        $projects = [];
        $baseDate = now();

        // 프로젝트 1 - 게임 백오피스
        $projects['nexon'] = Project::create([
            'code' => 'PRJ-' . $baseDate->copy()->subDays(20)->format('Ymd') . '-0001',
            'name' => '게임 백오피스 시스템',
            'description' => '게임 운영을 위한 종합 백오피스 관리 시스템 개발',
            'customer_id' => $customers['nexon']->id,
            'contract_id' => $contracts['nexon']->id,
            'manager_id' => $users['backend_lead']->id,
            'start_date' => $baseDate->copy()->subDays(20),
            'end_date' => $baseDate->copy()->addMonths(3),
            'budget' => 80000000,
            'actual_cost' => 12000000,
            'status' => '진행중',
            'progress' => 25,
            'priority' => '높음',
        ]);

        // 프로젝트 2 - 결제 API
        $projects['fintech'] = Project::create([
            'code' => 'PRJ-' . $baseDate->copy()->subDays(10)->format('Ymd') . '-0001',
            'name' => '결제 API 연동',
            'description' => 'PG사 결제 API 연동 및 커스터마이징 개발',
            'customer_id' => $customers['fintech']->id,
            'contract_id' => $contracts['fintech']->id,
            'manager_id' => $users['backend_lead']->id,
            'start_date' => $baseDate->copy()->subDays(10),
            'end_date' => $baseDate->copy()->addMonths(2),
            'budget' => 35000000,
            'actual_cost' => 5000000,
            'status' => '진행중',
            'progress' => 15,
            'priority' => '높음',
        ]);

        // 프로젝트 3 - 헬스케어 앱
        $projects['startup'] = Project::create([
            'code' => 'PRJ-' . $baseDate->format('Ymd') . '-0001',
            'name' => '헬스케어 앱 MVP',
            'description' => '헬스케어 모바일 앱 MVP 버전 개발',
            'customer_id' => $customers['startup']->id,
            'contract_id' => $contracts['startup']->id,
            'manager_id' => $users['frontend_lead']->id,
            'start_date' => $baseDate->copy(),
            'end_date' => $baseDate->copy()->addMonths(2),
            'budget' => 45000000,
            'actual_cost' => 0,
            'status' => '계획중',
            'progress' => 0,
            'priority' => '보통',
        ]);

        // 프로젝트 4 - 쇼핑몰 유지보수
        $projects['ecommerce'] = Project::create([
            'code' => 'PRJ-' . $baseDate->copy()->subMonths(1)->format('Ymd') . '-0001',
            'name' => '쇼핑몰 시스템 유지보수',
            'description' => '쇼핑몰 시스템 운영 및 유지보수',
            'customer_id' => $customers['ecommerce']->id,
            'contract_id' => $contracts['ecommerce']->id,
            'manager_id' => $users['backend_lead']->id,
            'start_date' => $baseDate->copy()->subMonths(1),
            'end_date' => $baseDate->copy()->addMonths(11),
            'budget' => 36000000,
            'actual_cost' => 3000000,
            'status' => '진행중',
            'progress' => 8,
            'priority' => '보통',
        ]);

        return $projects;
    }

    private function createMilestonesAndTasks(array $projects, array $users): void
    {
        $baseDate = now();

        // 프로젝트 1 - 게임 백오피스 마일스톤/태스크
        $m1 = Milestone::create([
            'project_id' => $projects['nexon']->id,
            'name' => '1단계: 요구사항 분석',
            'description' => '요구사항 수집 및 분석, 설계 문서 작성',
            'due_date' => $baseDate->copy()->subDays(10),
            'completed_date' => $baseDate->copy()->subDays(12),
            'status' => '완료',
            'sort_order' => 1,
        ]);

        Task::create([
            'project_id' => $projects['nexon']->id,
            'milestone_id' => $m1->id,
            'title' => '요구사항 인터뷰',
            'description' => '고객사 담당자 인터뷰 및 요구사항 수집',
            'assigned_to' => $users['planner']->id,
            'created_by' => $users['backend_lead']->id,
            'status' => '완료',
            'priority' => '높음',
            'start_date' => $baseDate->copy()->subDays(20),
            'due_date' => $baseDate->copy()->subDays(15),
            'completed_date' => $baseDate->copy()->subDays(16),
            'estimated_hours' => 16,
            'actual_hours' => 14,
        ]);

        Task::create([
            'project_id' => $projects['nexon']->id,
            'milestone_id' => $m1->id,
            'title' => '시스템 설계 문서 작성',
            'description' => 'ERD, 시스템 아키텍처 설계',
            'assigned_to' => $users['backend_lead']->id,
            'created_by' => $users['backend_lead']->id,
            'status' => '완료',
            'priority' => '높음',
            'start_date' => $baseDate->copy()->subDays(15),
            'due_date' => $baseDate->copy()->subDays(10),
            'completed_date' => $baseDate->copy()->subDays(11),
            'estimated_hours' => 24,
            'actual_hours' => 20,
        ]);

        $m2 = Milestone::create([
            'project_id' => $projects['nexon']->id,
            'name' => '2단계: 백엔드 개발',
            'description' => 'API 및 데이터베이스 개발',
            'due_date' => $baseDate->copy()->addDays(20),
            'status' => '진행중',
            'sort_order' => 2,
        ]);

        Task::create([
            'project_id' => $projects['nexon']->id,
            'milestone_id' => $m2->id,
            'title' => '데이터베이스 스키마 구축',
            'description' => 'MySQL 테이블 설계 및 생성',
            'assigned_to' => $users['backend_dev1']->id,
            'created_by' => $users['backend_lead']->id,
            'status' => '완료',
            'priority' => '높음',
            'start_date' => $baseDate->copy()->subDays(8),
            'due_date' => $baseDate->copy()->subDays(3),
            'completed_date' => $baseDate->copy()->subDays(4),
            'estimated_hours' => 16,
            'actual_hours' => 18,
        ]);

        Task::create([
            'project_id' => $projects['nexon']->id,
            'milestone_id' => $m2->id,
            'title' => '사용자 관리 API 개발',
            'description' => '회원, 권한 관리 CRUD API',
            'assigned_to' => $users['backend_dev1']->id,
            'created_by' => $users['backend_lead']->id,
            'status' => '진행중',
            'priority' => '높음',
            'start_date' => $baseDate->copy()->subDays(3),
            'due_date' => $baseDate->copy()->addDays(5),
            'estimated_hours' => 40,
            'actual_hours' => 16,
        ]);

        Task::create([
            'project_id' => $projects['nexon']->id,
            'milestone_id' => $m2->id,
            'title' => '게임 데이터 관리 API',
            'description' => '게임 아이템, 캐릭터 관리 API',
            'assigned_to' => $users['backend_dev2']->id,
            'created_by' => $users['backend_lead']->id,
            'status' => '할일',
            'priority' => '보통',
            'start_date' => $baseDate->copy()->addDays(5),
            'due_date' => $baseDate->copy()->addDays(15),
            'estimated_hours' => 48,
        ]);

        // 프로젝트 2 - 결제 API 마일스톤/태스크
        $m3 = Milestone::create([
            'project_id' => $projects['fintech']->id,
            'name' => 'PG사 API 분석',
            'description' => 'PG사 API 문서 분석 및 연동 설계',
            'due_date' => $baseDate->copy()->subDays(3),
            'completed_date' => $baseDate->copy()->subDays(4),
            'status' => '완료',
            'sort_order' => 1,
        ]);

        Task::create([
            'project_id' => $projects['fintech']->id,
            'milestone_id' => $m3->id,
            'title' => 'PG사 API 문서 분석',
            'description' => '토스페이먼츠 API 분석',
            'assigned_to' => $users['backend_lead']->id,
            'created_by' => $users['backend_lead']->id,
            'status' => '완료',
            'priority' => '높음',
            'start_date' => $baseDate->copy()->subDays(10),
            'due_date' => $baseDate->copy()->subDays(5),
            'completed_date' => $baseDate->copy()->subDays(6),
            'estimated_hours' => 8,
            'actual_hours' => 6,
        ]);

        $m4 = Milestone::create([
            'project_id' => $projects['fintech']->id,
            'name' => 'API 개발 및 테스트',
            'description' => '결제 API 개발 및 통합 테스트',
            'due_date' => $baseDate->copy()->addDays(30),
            'status' => '진행중',
            'sort_order' => 2,
        ]);

        Task::create([
            'project_id' => $projects['fintech']->id,
            'milestone_id' => $m4->id,
            'title' => '결제 요청 API 개발',
            'description' => '카드 결제 요청 API 구현',
            'assigned_to' => $users['backend_dev2']->id,
            'created_by' => $users['backend_lead']->id,
            'status' => '진행중',
            'priority' => '높음',
            'start_date' => $baseDate->copy()->subDays(2),
            'due_date' => $baseDate->copy()->addDays(10),
            'estimated_hours' => 32,
            'actual_hours' => 8,
        ]);
    }

    private function createTimesheets(array $projects, array $users): void
    {
        $baseDate = now();

        // 지난 3주간의 타임시트 생성
        for ($i = 21; $i >= 1; $i--) {
            $date = $baseDate->copy()->subDays($i);
            
            // 주말 제외
            if ($date->isWeekend()) {
                continue;
            }

            // 백엔드 리드
            Timesheet::create([
                'user_id' => $users['backend_lead']->id,
                'project_id' => $projects['nexon']->id,
                'date' => $date,
                'hours' => rand(6, 8),
                'description' => '게임 백오피스 개발 작업',
                'is_billable' => true,
                'hourly_rate' => 150000,
                'status' => $i > 7 ? '승인' : '대기',
                'approved_by' => $i > 7 ? $users['admin']->id : null,
                'approved_at' => $i > 7 ? $date->copy()->addDay() : null,
            ]);

            // 백엔드 개발자 1
            if ($i <= 15) {
                Timesheet::create([
                    'user_id' => $users['backend_dev1']->id,
                    'project_id' => $projects['nexon']->id,
                    'date' => $date,
                    'hours' => rand(7, 8),
                    'description' => 'DB 스키마 구축 및 API 개발',
                    'is_billable' => true,
                    'hourly_rate' => 100000,
                    'status' => $i > 7 ? '승인' : '대기',
                    'approved_by' => $i > 7 ? $users['backend_lead']->id : null,
                    'approved_at' => $i > 7 ? $date->copy()->addDay() : null,
                ]);
            }

            // 백엔드 개발자 2 - 결제 API 프로젝트
            if ($i <= 10) {
                Timesheet::create([
                    'user_id' => $users['backend_dev2']->id,
                    'project_id' => $projects['fintech']->id,
                    'date' => $date,
                    'hours' => rand(6, 8),
                    'description' => '결제 API 개발',
                    'is_billable' => true,
                    'hourly_rate' => 100000,
                    'status' => $i > 5 ? '승인' : '대기',
                    'approved_by' => $i > 5 ? $users['backend_lead']->id : null,
                    'approved_at' => $i > 5 ? $date->copy()->addDay() : null,
                ]);
            }

            // 기획자
            if ($i >= 15) {
                Timesheet::create([
                    'user_id' => $users['planner']->id,
                    'project_id' => $projects['nexon']->id,
                    'date' => $date,
                    'hours' => rand(4, 6),
                    'description' => '요구사항 분석 및 기획',
                    'is_billable' => true,
                    'hourly_rate' => 120000,
                    'status' => '승인',
                    'approved_by' => $users['admin']->id,
                    'approved_at' => $date->copy()->addDay(),
                ]);
            }
        }
    }

    private function createPurchaseOrders(array $suppliers, array $products, array $warehouses, array $users): void
    {
        $baseDate = now();

        // 발주 1 - IT 장비 구매 (입고 완료)
        $po1 = PurchaseOrder::create([
            'po_number' => 'PO-' . $baseDate->copy()->subDays(20)->format('Ymd') . '-0001',
            'supplier_id' => $suppliers['it_equipment']->id,
            'order_date' => $baseDate->copy()->subDays(20),
            'expected_date' => $baseDate->copy()->subDays(15),
            'received_date' => $baseDate->copy()->subDays(14),
            'subtotal' => 5250000,
            'tax_amount' => 525000,
            'total_amount' => 5775000,
            'status' => '입고완료',
            'note' => '신규 입사자용 장비',
            'shipping_address' => '서울시 강남구 테헤란로 123',
            'created_by' => $users['hr']->id,
            'approved_by' => $users['admin']->id,
            'approved_at' => $baseDate->copy()->subDays(19),
        ]);

        PurchaseOrderItem::create([
            'purchase_order_id' => $po1->id,
            'product_id' => $products['laptop']->id,
            'description' => 'MacBook Pro 14인치 M3 Pro',
            'quantity' => 1,
            'unit' => '대',
            'unit_price' => 2800000,
            'tax_rate' => 10,
            'amount' => 2800000,
            'received_quantity' => 1,
        ]);

        PurchaseOrderItem::create([
            'purchase_order_id' => $po1->id,
            'product_id' => $products['monitor']->id,
            'description' => 'Dell UltraSharp 27 4K',
            'quantity' => 2,
            'unit' => '대',
            'unit_price' => 650000,
            'tax_rate' => 10,
            'amount' => 1300000,
            'received_quantity' => 2,
        ]);

        PurchaseOrderItem::create([
            'purchase_order_id' => $po1->id,
            'product_id' => $products['keyboard']->id,
            'description' => '레오폴드 FC660M',
            'quantity' => 2,
            'unit' => '개',
            'unit_price' => 150000,
            'tax_rate' => 10,
            'amount' => 300000,
            'received_quantity' => 2,
        ]);

        // 발주 2 - 사무용품 (입고 완료)
        $po2 = PurchaseOrder::create([
            'po_number' => 'PO-' . $baseDate->copy()->subDays(10)->format('Ymd') . '-0001',
            'supplier_id' => $suppliers['office']->id,
            'order_date' => $baseDate->copy()->subDays(10),
            'expected_date' => $baseDate->copy()->subDays(7),
            'received_date' => $baseDate->copy()->subDays(8),
            'subtotal' => 298000,
            'tax_amount' => 29800,
            'total_amount' => 327800,
            'status' => '입고완료',
            'note' => '월간 사무용품',
            'shipping_address' => '서울시 강남구 테헤란로 123',
            'created_by' => $users['hr']->id,
            'approved_by' => $users['hr']->id,
            'approved_at' => $baseDate->copy()->subDays(10),
        ]);

        PurchaseOrderItem::create([
            'purchase_order_id' => $po2->id,
            'product_id' => $products['paper']->id,
            'description' => 'A4 복사용지 (2,500매)',
            'quantity' => 10,
            'unit' => '박스',
            'unit_price' => 25000,
            'tax_rate' => 10,
            'amount' => 250000,
            'received_quantity' => 10,
        ]);

        PurchaseOrderItem::create([
            'purchase_order_id' => $po2->id,
            'product_id' => $products['pen']->id,
            'description' => '모나미 볼펜 12개입',
            'quantity' => 6,
            'unit' => '세트',
            'unit_price' => 8000,
            'tax_rate' => 10,
            'amount' => 48000,
            'received_quantity' => 6,
        ]);

        // 재고 생성 (입고된 상품)
        Stock::create([
            'warehouse_id' => $warehouses['main']->id,
            'product_id' => $products['laptop']->id,
            'quantity' => 1,
            'reserved_quantity' => 0,
        ]);

        Stock::create([
            'warehouse_id' => $warehouses['main']->id,
            'product_id' => $products['monitor']->id,
            'quantity' => 2,
            'reserved_quantity' => 0,
        ]);

        Stock::create([
            'warehouse_id' => $warehouses['main']->id,
            'product_id' => $products['keyboard']->id,
            'quantity' => 2,
            'reserved_quantity' => 0,
        ]);

        Stock::create([
            'warehouse_id' => $warehouses['main']->id,
            'product_id' => $products['paper']->id,
            'quantity' => 10,
            'reserved_quantity' => 0,
        ]);

        Stock::create([
            'warehouse_id' => $warehouses['main']->id,
            'product_id' => $products['pen']->id,
            'quantity' => 6,
            'reserved_quantity' => 0,
        ]);

        // 발주 3 - 추가 장비 (발주 승인 대기)
        $po3 = PurchaseOrder::create([
            'po_number' => 'PO-' . $baseDate->format('Ymd') . '-0001',
            'supplier_id' => $suppliers['it_equipment']->id,
            'order_date' => $baseDate->copy(),
            'expected_date' => $baseDate->copy()->addDays(5),
            'subtotal' => 2950000,
            'tax_amount' => 295000,
            'total_amount' => 3245000,
            'status' => '승인대기',
            'note' => '추가 모니터 및 주변기기',
            'shipping_address' => '서울시 강남구 테헤란로 123',
            'created_by' => $users['hr']->id,
        ]);

        PurchaseOrderItem::create([
            'purchase_order_id' => $po3->id,
            'product_id' => $products['monitor']->id,
            'description' => 'Dell UltraSharp 27 4K',
            'quantity' => 3,
            'unit' => '대',
            'unit_price' => 650000,
            'tax_rate' => 10,
            'amount' => 1950000,
            'received_quantity' => 0,
        ]);

        PurchaseOrderItem::create([
            'purchase_order_id' => $po3->id,
            'product_id' => $products['keyboard']->id,
            'description' => '레오폴드 FC660M',
            'quantity' => 5,
            'unit' => '개',
            'unit_price' => 150000,
            'tax_rate' => 10,
            'amount' => 750000,
            'received_quantity' => 0,
        ]);
    }

    private function createInvoicesAndPayments(array $customers, array $contracts, array $projects, array $products, array $users): void
    {
        $baseDate = now();

        // 청구서 1 - 게임 백오피스 착수금 (결제 완료)
        $inv1 = Invoice::create([
            'invoice_number' => 'INV-' . $baseDate->copy()->subDays(18)->format('Ymd') . '-0001',
            'customer_id' => $customers['nexon']->id,
            'contract_id' => $contracts['nexon']->id,
            'project_id' => $projects['nexon']->id,
            'issue_date' => $baseDate->copy()->subDays(18),
            'due_date' => $baseDate->copy()->subDays(3),
            'subtotal' => 24000000,
            'tax_amount' => 2400000,
            'total_amount' => 26400000,
            'paid_amount' => 26400000,
            'status' => '결제완료',
            'note' => '게임 백오피스 시스템 개발 착수금 (30%)',
            'terms' => '발행일로부터 15일 이내',
            'created_by' => $users['accountant']->id,
        ]);

        InvoiceItem::create([
            'invoice_id' => $inv1->id,
            'product_id' => $products['web_dev']->id,
            'description' => '게임 백오피스 시스템 개발 착수금 (30%)',
            'quantity' => 1,
            'unit' => '건',
            'unit_price' => 24000000,
            'discount' => 0,
            'tax_rate' => 10,
            'amount' => 24000000,
        ]);

        // 결제 내역
        Payment::create([
            'payment_number' => 'PAY-' . $baseDate->copy()->subDays(5)->format('Ymd') . '-0001',
            'payable_type' => Invoice::class,
            'payable_id' => $inv1->id,
            'payment_date' => $baseDate->copy()->subDays(5),
            'amount' => 26400000,
            'method' => '계좌이체',
            'reference' => '넥스트게임즈 -> 테크웨이브',
            'note' => '착수금 입금 완료',
            'recorded_by' => $users['accountant']->id,
        ]);

        // 청구서 2 - 결제 API 착수금 (결제 대기)
        $inv2 = Invoice::create([
            'invoice_number' => 'INV-' . $baseDate->copy()->subDays(8)->format('Ymd') . '-0001',
            'customer_id' => $customers['fintech']->id,
            'contract_id' => $contracts['fintech']->id,
            'project_id' => $projects['fintech']->id,
            'issue_date' => $baseDate->copy()->subDays(8),
            'due_date' => $baseDate->copy()->addDays(7),
            'subtotal' => 17500000,
            'tax_amount' => 1750000,
            'total_amount' => 19250000,
            'paid_amount' => 0,
            'status' => '발행',
            'note' => '결제 API 연동 개발 착수금 (50%)',
            'terms' => '발행일로부터 15일 이내',
            'created_by' => $users['accountant']->id,
        ]);

        InvoiceItem::create([
            'invoice_id' => $inv2->id,
            'product_id' => $products['api_dev']->id,
            'description' => '결제 API 연동 개발 착수금 (50%)',
            'quantity' => 1,
            'unit' => '건',
            'unit_price' => 17500000,
            'discount' => 0,
            'tax_rate' => 10,
            'amount' => 17500000,
        ]);

        // 청구서 3 - 유지보수 월정액 (부분 결제)
        $inv3 = Invoice::create([
            'invoice_number' => 'INV-' . $baseDate->copy()->subDays(25)->format('Ymd') . '-0001',
            'customer_id' => $customers['ecommerce']->id,
            'contract_id' => $contracts['ecommerce']->id,
            'project_id' => $projects['ecommerce']->id,
            'issue_date' => $baseDate->copy()->subDays(25),
            'due_date' => $baseDate->copy()->subDays(10),
            'subtotal' => 3000000,
            'tax_amount' => 300000,
            'total_amount' => 3300000,
            'paid_amount' => 3300000,
            'status' => '결제완료',
            'note' => '1월 유지보수 비용',
            'terms' => '발행일로부터 15일 이내',
            'created_by' => $users['accountant']->id,
        ]);

        InvoiceItem::create([
            'invoice_id' => $inv3->id,
            'product_id' => $products['monthly_maintenance']->id,
            'description' => '쇼핑몰 시스템 월간 유지보수 (1월)',
            'quantity' => 1,
            'unit' => '월',
            'unit_price' => 3000000,
            'discount' => 0,
            'tax_rate' => 10,
            'amount' => 3000000,
        ]);

        Payment::create([
            'payment_number' => 'PAY-' . $baseDate->copy()->subDays(12)->format('Ymd') . '-0001',
            'payable_type' => Invoice::class,
            'payable_id' => $inv3->id,
            'payment_date' => $baseDate->copy()->subDays(12),
            'amount' => 3300000,
            'method' => '계좌이체',
            'reference' => '쇼핑몰플러스 -> 테크웨이브',
            'note' => '1월 유지보수 비용',
            'recorded_by' => $users['accountant']->id,
        ]);

        // 청구서 4 - 이번 달 유지보수 (발행 예정)
        Invoice::create([
            'invoice_number' => 'INV-' . $baseDate->format('Ymd') . '-0001',
            'customer_id' => $customers['ecommerce']->id,
            'contract_id' => $contracts['ecommerce']->id,
            'project_id' => $projects['ecommerce']->id,
            'issue_date' => $baseDate->copy(),
            'due_date' => $baseDate->copy()->addDays(15),
            'subtotal' => 3000000,
            'tax_amount' => 300000,
            'total_amount' => 3300000,
            'paid_amount' => 0,
            'status' => '초안',
            'note' => '2월 유지보수 비용',
            'terms' => '발행일로부터 15일 이내',
            'created_by' => $users['accountant']->id,
        ]);
    }

    private function createExpenses(array $users, array $projects, array $suppliers): void
    {
        $baseDate = now();
        $categories = ExpenseCategory::all();
        
        $officeCategory = $categories->where('name', '사무용품')->first();
        $itCategory = $categories->where('name', '소프트웨어')->first() ?? $categories->first();
        $meetingCategory = $categories->where('name', '식대')->first() ?? $categories->first();
        $travelCategory = $categories->where('name', '교통비')->first() ?? $categories->first();

        // 비용 1 - 클라우드 서비스 비용
        Expense::create([
            'expense_number' => 'EXP-' . $baseDate->copy()->subDays(15)->format('Ymd') . '-0001',
            'category_id' => $itCategory?->id,
            'employee_id' => Employee::where('user_id', $users['admin']->id)->first()?->id,
            'supplier_id' => $suppliers['cloud']->id,
            'expense_date' => $baseDate->copy()->subDays(15),
            'title' => 'AWS 클라우드 서비스 1월분',
            'description' => 'EC2, RDS, S3 등 클라우드 인프라 비용',
            'amount' => 850000,
            'tax_amount' => 85000,
            'total_amount' => 935000,
            'status' => '승인',
            'approved_by' => $users['ceo']->id,
            'approved_at' => $baseDate->copy()->subDays(14),
        ]);

        // 비용 2 - 고객 미팅 식대
        Expense::create([
            'expense_number' => 'EXP-' . $baseDate->copy()->subDays(10)->format('Ymd') . '-0001',
            'category_id' => $meetingCategory?->id,
            'employee_id' => Employee::where('user_id', $users['sales']->id)->first()?->id,
            'project_id' => $projects['nexon']->id,
            'expense_date' => $baseDate->copy()->subDays(10),
            'title' => '넥스트게임즈 킥오프 미팅 식대',
            'description' => '프로젝트 킥오프 미팅 점심 식사',
            'amount' => 180000,
            'tax_amount' => 18000,
            'total_amount' => 198000,
            'status' => '승인',
            'approved_by' => $users['admin']->id,
            'approved_at' => $baseDate->copy()->subDays(9),
        ]);

        // 비용 3 - 교통비
        Expense::create([
            'expense_number' => 'EXP-' . $baseDate->copy()->subDays(7)->format('Ymd') . '-0001',
            'category_id' => $travelCategory?->id,
            'employee_id' => Employee::where('user_id', $users['backend_lead']->id)->first()?->id,
            'project_id' => $projects['fintech']->id,
            'expense_date' => $baseDate->copy()->subDays(7),
            'title' => '핀테크솔루션 방문 교통비',
            'description' => '고객사 방문 택시비',
            'amount' => 45000,
            'tax_amount' => 4500,
            'total_amount' => 49500,
            'status' => '승인',
            'approved_by' => $users['admin']->id,
            'approved_at' => $baseDate->copy()->subDays(6),
        ]);

        // 비용 4 - 사무용품 구매
        Expense::create([
            'expense_number' => 'EXP-' . $baseDate->copy()->subDays(5)->format('Ymd') . '-0001',
            'category_id' => $officeCategory?->id,
            'employee_id' => Employee::where('user_id', $users['hr']->id)->first()?->id,
            'supplier_id' => $suppliers['office']->id,
            'expense_date' => $baseDate->copy()->subDays(5),
            'title' => '사무용품 구매',
            'description' => '화이트보드 마커, 포스트잇 등',
            'amount' => 120000,
            'tax_amount' => 12000,
            'total_amount' => 132000,
            'status' => '승인',
            'approved_by' => $users['accountant']->id,
            'approved_at' => $baseDate->copy()->subDays(4),
        ]);

        // 비용 5 - 승인 대기 중
        Expense::create([
            'expense_number' => 'EXP-' . $baseDate->copy()->subDays(2)->format('Ymd') . '-0001',
            'category_id' => $meetingCategory?->id,
            'employee_id' => Employee::where('user_id', $users['planner']->id)->first()?->id,
            'project_id' => $projects['startup']->id,
            'expense_date' => $baseDate->copy()->subDays(2),
            'title' => '헬스케어랩 기획 미팅',
            'description' => '요구사항 협의 미팅 커피',
            'amount' => 35000,
            'tax_amount' => 3500,
            'total_amount' => 38500,
            'status' => '대기',
        ]);
    }
}
