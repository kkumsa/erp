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
use App\Models\Lead;
use App\Models\Milestone;
use App\Models\Opportunity;
use App\Models\Payment;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\Project;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\Stock;
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
        $this->command->info('테크웨이브 조직 샘플 데이터 생성 중...');

        // 1. 부서 생성
        $departments = $this->createDepartments();
        $this->command->info('✓ 부서 생성 완료 (2본부 + 2직속팀)');

        // 2. 사용자 및 직원 생성
        $users = $this->createUsersAndEmployees($departments);
        $this->command->info('✓ 사용자/직원 생성 완료 (15명)');

        // 3. 창고 생성
        $warehouses = $this->createWarehouses($users);
        $this->command->info('✓ 창고 생성 완료');

        // 4. 상품 카테고리 및 상품 생성
        $products = $this->createProductsAndCategories();
        $this->command->info('✓ 상품/카테고리 생성 완료 (SW/HW)');

        // 5. 공급업체 생성
        $suppliers = $this->createSuppliers();
        $this->command->info('✓ 공급업체 생성 완료');

        // 6. 고객사 및 담당자 생성
        $customers = $this->createCustomersAndContacts($users);
        $this->command->info('✓ 고객사 생성 완료');

        // 7. 리드 생성
        $this->createLeads($users);
        $this->command->info('✓ 리드 생성 완료');

        // 8. 영업 기회 생성
        $this->createOpportunities($customers, $users);
        $this->command->info('✓ 영업 기회 생성 완료');

        // 9. 계약 생성
        $contracts = $this->createContracts($customers, $users);
        $this->command->info('✓ 계약 생성 완료');

        // 10. 프로젝트 생성
        $projects = $this->createProjects($customers, $contracts, $users);
        $this->command->info('✓ 프로젝트 생성 완료');

        // 11. 마일스톤 및 태스크 생성
        $this->createMilestonesAndTasks($projects, $users);
        $this->command->info('✓ 마일스톤/태스크 생성 완료');

        // 12. 타임시트 생성
        $this->createTimesheets($projects, $users);
        $this->command->info('✓ 타임시트 생성 완료');

        // 13. 발주서 생성
        $this->createPurchaseOrders($suppliers, $products, $warehouses, $users);
        $this->command->info('✓ 발주서 생성 완료');

        // 14. 청구서 및 결제 생성
        $this->createInvoicesAndPayments($customers, $contracts, $projects, $products, $users);
        $this->command->info('✓ 청구서/결제 생성 완료');

        // 15. 비용 생성
        $this->createExpenses($users, $projects, $suppliers);
        $this->command->info('✓ 비용 생성 완료');

        $this->command->info('');
        $this->command->info('🎉 테크웨이브 샘플 데이터 생성 완료!');
        $this->command->info('');
        $this->command->info('=== 로그인 계정 목록 ===');
        $this->command->info('CEO:           admin@techwave.kr   / password');
        $this->command->info('SD 본부장:      sd@techwave.kr      / password');
        $this->command->info('HD 본부장:      hd@techwave.kr      / password');
        $this->command->info('기획전략실장:    strategy@techwave.kr / password');
        $this->command->info('경영지원팀장:    finance@techwave.kr  / password');
        $this->command->info('개발팀장:       dev.lead@techwave.kr / password');
        $this->command->info('AI팀장:        ai.lead@techwave.kr  / password');
        $this->command->info('설계팀장:       hw.lead@techwave.kr  / password');
        $this->command->info('영업팀장:       emb.lead@techwave.kr / password');
        $this->command->info('(전체 비밀번호: password)');
    }

    // ─────────────────────────────────────────────
    // 1. 부서 생성 - 2본부(SW/HW) + 2 CEO 직속팀
    // ─────────────────────────────────────────────
    private function createDepartments(): array
    {
        $departments = [];

        // ── 소프트웨어 본부 (SD) ──
        $sd = Department::create([
            'name' => '소프트웨어 본부',
            'code' => 'SD',
            'description' => '소프트웨어 개발 총괄 - UX, 데이터, 서비스 개발',
            'is_active' => true,
            'sort_order' => 1,
        ]);
        $departments['sd'] = $sd;

        $departments['dev_team'] = Department::create([
            'name' => '개발팀',
            'code' => 'SD-DEV',
            'description' => '웹/앱 서비스 개발 (Frontend/Backend)',
            'parent_id' => $sd->id,
            'is_active' => true,
            'sort_order' => 2,
        ]);

        $departments['ai_team'] = Department::create([
            'name' => 'AI팀',
            'code' => 'SD-AI',
            'description' => 'STT, LLM, 데이터 분석, AEO 연구',
            'parent_id' => $sd->id,
            'is_active' => true,
            'sort_order' => 3,
        ]);

        // ── 하드웨어 본부 (HD) ──
        $hd = Department::create([
            'name' => '하드웨어 본부',
            'code' => 'HD',
            'description' => '하드웨어 설계, 생산 프로세스 총괄',
            'is_active' => true,
            'sort_order' => 4,
        ]);
        $departments['hd'] = $hd;

        $departments['design_team'] = Department::create([
            'name' => '설계팀',
            'code' => 'HD-ENG',
            'description' => '기구 설계, 회로 설계(PCB), 센서 데이터 수집 로직',
            'parent_id' => $hd->id,
            'is_active' => true,
            'sort_order' => 5,
        ]);

        $departments['emb_team'] = Department::create([
            'name' => '임베디드SW팀',
            'code' => 'HD-EMB',
            'description' => '하드웨어 칩셋 펌웨어(Firmware) 개발',
            'parent_id' => $hd->id,
            'is_active' => true,
            'sort_order' => 6,
        ]);

        // ── CEO 직속팀 ──
        $departments['strategy'] = Department::create([
            'name' => '기획전략실',
            'code' => 'STR',
            'description' => 'CEO 직속 - 전략기획, 인사관리',
            'is_active' => true,
            'sort_order' => 7,
        ]);

        $departments['management'] = Department::create([
            'name' => '경영지원팀',
            'code' => 'MGT',
            'description' => 'CEO 직속 - 재무, 총무, 경영지원',
            'is_active' => true,
            'sort_order' => 8,
        ]);

        return $departments;
    }

    // ─────────────────────────────────────────────
    // 2. 사용자 및 직원 생성 - 총 15명
    // ─────────────────────────────────────────────
    private function createUsersAndEmployees(array $departments): array
    {
        $users = [];
        $baseDate = now()->subMonth();

        // ══════════════════════════════════════
        // CEO (Super Admin) - 1명
        // ══════════════════════════════════════
        $ceo = User::create([
            'name' => '김대표',
            'email' => 'admin@techwave.kr',
            'password' => Hash::make('password'),
            'is_active' => true,
        ]);
        $ceo->assignRole('Super Admin');
        Employee::create([
            'user_id' => $ceo->id,
            'department_id' => $departments['strategy']->id,
            'employee_code' => 'TW-001',
            'position' => '대표',
            'job_title' => '대표이사 (CEO)',
            'hire_date' => $baseDate->copy()->subYears(5),
            'birth_date' => '1978-03-15',
            'phone' => '010-1000-0001',
            'employment_type' => '정규직',
            'status' => '재직',
            'base_salary' => 20000000,
            'annual_leave_days' => 25,
        ]);
        $users['ceo'] = $ceo;

        // ══════════════════════════════════════
        // ① 소프트웨어 본부 (SD)
        // ══════════════════════════════════════

        // SD 본부장 (Admin)
        $sdHead = User::create([
            'name' => '박소프트',
            'email' => 'sd@techwave.kr',
            'password' => Hash::make('password'),
            'is_active' => true,
        ]);
        $sdHead->assignRole('Admin');
        Employee::create([
            'user_id' => $sdHead->id,
            'department_id' => $departments['sd']->id,
            'employee_code' => 'TW-100',
            'position' => '본부장',
            'job_title' => '소프트웨어 본부장',
            'hire_date' => $baseDate->copy()->subYears(3),
            'birth_date' => '1982-07-22',
            'phone' => '010-1000-0100',
            'employment_type' => '정규직',
            'status' => '재직',
            'base_salary' => 12000000,
            'annual_leave_days' => 22,
        ]);
        $departments['sd']->update(['manager_id' => $sdHead->id]);
        $users['sd_head'] = $sdHead;

        // 개발팀장 (Manager)
        $devLead = User::create([
            'name' => '최개발',
            'email' => 'dev.lead@techwave.kr',
            'password' => Hash::make('password'),
            'is_active' => true,
        ]);
        $devLead->assignRole('Manager');
        Employee::create([
            'user_id' => $devLead->id,
            'department_id' => $departments['dev_team']->id,
            'employee_code' => 'TW-110',
            'position' => '팀장',
            'job_title' => '개발팀장',
            'hire_date' => $baseDate->copy()->subYears(2),
            'birth_date' => '1988-11-05',
            'phone' => '010-1000-0110',
            'employment_type' => '정규직',
            'status' => '재직',
            'base_salary' => 8000000,
            'annual_leave_days' => 18,
        ]);
        $departments['dev_team']->update(['manager_id' => $devLead->id]);
        $users['dev_lead'] = $devLead;

        // 개발팀원 (Employee)
        $devMember = User::create([
            'name' => '강코딩',
            'email' => 'dev1@techwave.kr',
            'password' => Hash::make('password'),
            'is_active' => true,
        ]);
        $devMember->assignRole('Employee');
        Employee::create([
            'user_id' => $devMember->id,
            'department_id' => $departments['dev_team']->id,
            'employee_code' => 'TW-111',
            'position' => '사원',
            'job_title' => '풀스택 개발자',
            'hire_date' => $baseDate->copy()->subYear(),
            'birth_date' => '1995-04-12',
            'phone' => '010-1000-0111',
            'employment_type' => '정규직',
            'status' => '재직',
            'base_salary' => 4800000,
            'annual_leave_days' => 15,
        ]);
        $users['dev_member'] = $devMember;

        // AI팀장 (Manager)
        $aiLead = User::create([
            'name' => '이에아이',
            'email' => 'ai.lead@techwave.kr',
            'password' => Hash::make('password'),
            'is_active' => true,
        ]);
        $aiLead->assignRole('Manager');
        Employee::create([
            'user_id' => $aiLead->id,
            'department_id' => $departments['ai_team']->id,
            'employee_code' => 'TW-120',
            'position' => '팀장',
            'job_title' => 'AI팀장',
            'hire_date' => $baseDate->copy()->subMonths(18),
            'birth_date' => '1990-06-18',
            'phone' => '010-1000-0120',
            'employment_type' => '정규직',
            'status' => '재직',
            'base_salary' => 8500000,
            'annual_leave_days' => 18,
        ]);
        $departments['ai_team']->update(['manager_id' => $aiLead->id]);
        $users['ai_lead'] = $aiLead;

        // AI팀원 (Employee)
        $aiMember = User::create([
            'name' => '정데이터',
            'email' => 'ai1@techwave.kr',
            'password' => Hash::make('password'),
            'is_active' => true,
        ]);
        $aiMember->assignRole('Employee');
        Employee::create([
            'user_id' => $aiMember->id,
            'department_id' => $departments['ai_team']->id,
            'employee_code' => 'TW-121',
            'position' => '사원',
            'job_title' => 'AI 엔지니어',
            'hire_date' => $baseDate->copy()->subMonths(8),
            'birth_date' => '1996-09-23',
            'phone' => '010-1000-0121',
            'employment_type' => '정규직',
            'status' => '재직',
            'base_salary' => 5000000,
            'annual_leave_days' => 15,
        ]);
        $users['ai_member'] = $aiMember;

        // ══════════════════════════════════════
        // ② 하드웨어 본부 (HD)
        // ══════════════════════════════════════

        // HD 본부장 (Admin)
        $hdHead = User::create([
            'name' => '한하드',
            'email' => 'hd@techwave.kr',
            'password' => Hash::make('password'),
            'is_active' => true,
        ]);
        $hdHead->assignRole('Admin');
        Employee::create([
            'user_id' => $hdHead->id,
            'department_id' => $departments['hd']->id,
            'employee_code' => 'TW-200',
            'position' => '본부장',
            'job_title' => '하드웨어 본부장',
            'hire_date' => $baseDate->copy()->subYears(3),
            'birth_date' => '1981-02-28',
            'phone' => '010-1000-0200',
            'employment_type' => '정규직',
            'status' => '재직',
            'base_salary' => 12000000,
            'annual_leave_days' => 22,
        ]);
        $departments['hd']->update(['manager_id' => $hdHead->id]);
        $users['hd_head'] = $hdHead;

        // 설계팀장 (Manager)
        $hwLead = User::create([
            'name' => '오설계',
            'email' => 'hw.lead@techwave.kr',
            'password' => Hash::make('password'),
            'is_active' => true,
        ]);
        $hwLead->assignRole('Manager');
        Employee::create([
            'user_id' => $hwLead->id,
            'department_id' => $departments['design_team']->id,
            'employee_code' => 'TW-210',
            'position' => '팀장',
            'job_title' => '설계팀장',
            'hire_date' => $baseDate->copy()->subYears(2),
            'birth_date' => '1987-10-08',
            'phone' => '010-1000-0210',
            'employment_type' => '정규직',
            'status' => '재직',
            'base_salary' => 7500000,
            'annual_leave_days' => 18,
        ]);
        $departments['design_team']->update(['manager_id' => $hwLead->id]);
        $users['hw_lead'] = $hwLead;

        // 설계팀원 (Employee)
        $hwMember = User::create([
            'name' => '류기구',
            'email' => 'hw1@techwave.kr',
            'password' => Hash::make('password'),
            'is_active' => true,
        ]);
        $hwMember->assignRole('Employee');
        Employee::create([
            'user_id' => $hwMember->id,
            'department_id' => $departments['design_team']->id,
            'employee_code' => 'TW-211',
            'position' => '사원',
            'job_title' => 'PCB 설계 엔지니어',
            'hire_date' => $baseDate->copy()->subMonths(10),
            'birth_date' => '1994-01-17',
            'phone' => '010-1000-0211',
            'employment_type' => '정규직',
            'status' => '재직',
            'base_salary' => 4500000,
            'annual_leave_days' => 15,
        ]);
        $users['hw_member'] = $hwMember;

        // 임베디드SW팀장 (Manager)
        $embLead = User::create([
            'name' => '조펌웨',
            'email' => 'emb.lead@techwave.kr',
            'password' => Hash::make('password'),
            'is_active' => true,
        ]);
        $embLead->assignRole('Manager');
        Employee::create([
            'user_id' => $embLead->id,
            'department_id' => $departments['emb_team']->id,
            'employee_code' => 'TW-220',
            'position' => '팀장',
            'job_title' => '임베디드SW팀장',
            'hire_date' => $baseDate->copy()->subYears(2),
            'birth_date' => '1989-05-30',
            'phone' => '010-1000-0220',
            'employment_type' => '정규직',
            'status' => '재직',
            'base_salary' => 7000000,
            'annual_leave_days' => 18,
        ]);
        $departments['emb_team']->update(['manager_id' => $embLead->id]);
        $users['emb_lead'] = $embLead;

        // 임베디드SW팀원 (Employee)
        $embMember = User::create([
            'name' => '문펌웨어',
            'email' => 'emb1@techwave.kr',
            'password' => Hash::make('password'),
            'is_active' => true,
        ]);
        $embMember->assignRole('Employee');
        Employee::create([
            'user_id' => $embMember->id,
            'department_id' => $departments['emb_team']->id,
            'employee_code' => 'TW-221',
            'position' => '사원',
            'job_title' => '임베디드 개발자',
            'hire_date' => $baseDate->copy()->subMonths(6),
            'birth_date' => '1993-12-01',
            'phone' => '010-1000-0221',
            'employment_type' => '정규직',
            'status' => '재직',
            'base_salary' => 4500000,
            'annual_leave_days' => 15,
        ]);
        $users['emb_member'] = $embMember;

        // ══════════════════════════════════════
        // ③ 기획전략실 (CEO 직속, 인사)
        // ══════════════════════════════════════

        // 기획전략실장 (Admin + HR Manager)
        $strategyHead = User::create([
            'name' => '서전략',
            'email' => 'strategy@techwave.kr',
            'password' => Hash::make('password'),
            'is_active' => true,
        ]);
        $strategyHead->assignRole(['Admin', 'HR Manager']);
        Employee::create([
            'user_id' => $strategyHead->id,
            'department_id' => $departments['strategy']->id,
            'employee_code' => 'TW-300',
            'position' => '실장',
            'job_title' => '기획전략실장',
            'hire_date' => $baseDate->copy()->subYears(2),
            'birth_date' => '1985-08-14',
            'phone' => '010-1000-0300',
            'employment_type' => '정규직',
            'status' => '재직',
            'base_salary' => 9000000,
            'annual_leave_days' => 20,
        ]);
        $departments['strategy']->update(['manager_id' => $strategyHead->id]);
        $users['strategy_head'] = $strategyHead;

        // 기획전략실 팀원 (Employee + HR Manager)
        $hrMember = User::create([
            'name' => '윤인사',
            'email' => 'hr1@techwave.kr',
            'password' => Hash::make('password'),
            'is_active' => true,
        ]);
        $hrMember->assignRole(['Employee', 'HR Manager']);
        Employee::create([
            'user_id' => $hrMember->id,
            'department_id' => $departments['strategy']->id,
            'employee_code' => 'TW-301',
            'position' => '사원',
            'job_title' => '인사담당',
            'hire_date' => $baseDate->copy()->subYear(),
            'birth_date' => '1992-04-10',
            'phone' => '010-1000-0301',
            'employment_type' => '정규직',
            'status' => '재직',
            'base_salary' => 4200000,
            'annual_leave_days' => 15,
        ]);
        $users['hr_member'] = $hrMember;

        // ══════════════════════════════════════
        // ④ 경영지원팀 (CEO 직속, 재무)
        // ══════════════════════════════════════

        // 경영지원팀장 (Admin + Accountant)
        $financeHead = User::create([
            'name' => '정재무',
            'email' => 'finance@techwave.kr',
            'password' => Hash::make('password'),
            'is_active' => true,
        ]);
        $financeHead->assignRole(['Admin', 'Accountant']);
        Employee::create([
            'user_id' => $financeHead->id,
            'department_id' => $departments['management']->id,
            'employee_code' => 'TW-400',
            'position' => '팀장',
            'job_title' => '경영지원팀장',
            'hire_date' => $baseDate->copy()->subYears(2),
            'birth_date' => '1986-02-14',
            'phone' => '010-1000-0400',
            'employment_type' => '정규직',
            'status' => '재직',
            'base_salary' => 8000000,
            'annual_leave_days' => 20,
        ]);
        $departments['management']->update(['manager_id' => $financeHead->id]);
        $users['finance_head'] = $financeHead;

        // 경영지원팀 팀원 (Employee + Accountant)
        $accountMember = User::create([
            'name' => '신경리',
            'email' => 'account1@techwave.kr',
            'password' => Hash::make('password'),
            'is_active' => true,
        ]);
        $accountMember->assignRole(['Employee', 'Accountant']);
        Employee::create([
            'user_id' => $accountMember->id,
            'department_id' => $departments['management']->id,
            'employee_code' => 'TW-401',
            'position' => '사원',
            'job_title' => '경리담당',
            'hire_date' => $baseDate->copy()->subMonths(8),
            'birth_date' => '1994-11-25',
            'phone' => '010-1000-0401',
            'employment_type' => '정규직',
            'status' => '재직',
            'base_salary' => 4000000,
            'annual_leave_days' => 15,
        ]);
        $users['account_member'] = $accountMember;

        return $users;
    }

    // ─────────────────────────────────────────────
    // 3. 창고 생성
    // ─────────────────────────────────────────────
    private function createWarehouses(array $users): array
    {
        $warehouses = [];

        $warehouses['main'] = Warehouse::create([
            'name' => '본사 창고',
            'code' => 'WH-MAIN',
            'address' => '서울시 강남구 테헤란로 123, 테크웨이브빌딩 B1',
            'phone' => '02-1234-5678',
            'manager_id' => $users['finance_head']->id,
            'is_active' => true,
            'is_default' => true,
            'note' => '본사 사무실 내 물품 보관 및 HW 부품 창고',
        ]);

        $warehouses['hw_lab'] = Warehouse::create([
            'name' => 'HW 연구소 창고',
            'code' => 'WH-HWLAB',
            'address' => '서울시 강남구 테헤란로 123, 테크웨이브빌딩 3F',
            'phone' => '02-1234-5679',
            'manager_id' => $users['hd_head']->id,
            'is_active' => true,
            'is_default' => false,
            'note' => '하드웨어 본부 시제품 및 부품 보관',
        ]);

        return $warehouses;
    }

    // ─────────────────────────────────────────────
    // 4. 상품 카테고리 및 상품 생성 (SW/HW)
    // ─────────────────────────────────────────────
    private function createProductsAndCategories(): array
    {
        $products = [];

        // ══════════════════════════════════════
        // SW 카테고리 (최상위)
        // ══════════════════════════════════════
        $sw = ProductCategory::create([
            'name' => 'SW (소프트웨어)',
            'code' => 'SW',
            'description' => '소프트웨어 개발 및 서비스',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $swNew = ProductCategory::create([
            'name' => '신규 개발',
            'code' => 'SW-NEW',
            'description' => '신규 소프트웨어 개발 서비스',
            'parent_id' => $sw->id,
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $swMaint = ProductCategory::create([
            'name' => '유지보수',
            'code' => 'SW-MNT',
            'description' => '기존 시스템 유지보수 서비스',
            'parent_id' => $sw->id,
            'is_active' => true,
            'sort_order' => 2,
        ]);

        // SW 신규 개발 상품
        $products['web_dev'] = Product::create([
            'code' => 'SW-N-001',
            'name' => '웹 애플리케이션 개발',
            'category_id' => $swNew->id,
            'description' => '맞춤형 웹 애플리케이션 신규 개발 (프론트엔드+백엔드)',
            'unit' => '건',
            'purchase_price' => 0,
            'selling_price' => 50000000,
            'is_active' => true,
            'is_stockable' => false,
        ]);

        $products['mobile_dev'] = Product::create([
            'code' => 'SW-N-002',
            'name' => '모바일 앱 개발',
            'category_id' => $swNew->id,
            'description' => 'iOS/Android 크로스플랫폼 앱 개발',
            'unit' => '건',
            'purchase_price' => 0,
            'selling_price' => 40000000,
            'is_active' => true,
            'is_stockable' => false,
        ]);

        $products['api_dev'] = Product::create([
            'code' => 'SW-N-003',
            'name' => 'API 연동 개발',
            'category_id' => $swNew->id,
            'description' => 'REST/GraphQL API 개발 및 외부 시스템 연동',
            'unit' => '건',
            'purchase_price' => 0,
            'selling_price' => 20000000,
            'is_active' => true,
            'is_stockable' => false,
        ]);

        $products['ai_solution'] = Product::create([
            'code' => 'SW-N-004',
            'name' => 'AI 솔루션 개발',
            'category_id' => $swNew->id,
            'description' => 'LLM 기반 AI 솔루션, STT/TTS, 데이터 분석',
            'unit' => '건',
            'purchase_price' => 0,
            'selling_price' => 80000000,
            'is_active' => true,
            'is_stockable' => false,
        ]);

        $products['erp_dev'] = Product::create([
            'code' => 'SW-N-005',
            'name' => 'ERP/CRM 시스템 개발',
            'category_id' => $swNew->id,
            'description' => '기업 자원 관리 시스템 맞춤 개발',
            'unit' => '건',
            'purchase_price' => 0,
            'selling_price' => 100000000,
            'is_active' => true,
            'is_stockable' => false,
        ]);

        // SW 유지보수 상품
        $products['monthly_maint'] = Product::create([
            'code' => 'SW-M-001',
            'name' => '월간 유지보수',
            'category_id' => $swMaint->id,
            'description' => '시스템 월간 유지보수 (버그픽스, 모니터링, 업데이트)',
            'unit' => '월',
            'purchase_price' => 0,
            'selling_price' => 3000000,
            'is_active' => true,
            'is_stockable' => false,
        ]);

        $products['annual_maint'] = Product::create([
            'code' => 'SW-M-002',
            'name' => '연간 유지보수 계약',
            'category_id' => $swMaint->id,
            'description' => '연간 유지보수 계약 (정기점검, 긴급대응, SLA 보장)',
            'unit' => '년',
            'purchase_price' => 0,
            'selling_price' => 30000000,
            'is_active' => true,
            'is_stockable' => false,
        ]);

        $products['consulting'] = Product::create([
            'code' => 'SW-M-003',
            'name' => 'IT 컨설팅',
            'category_id' => $swMaint->id,
            'description' => 'IT 전략, 아키텍처, 보안 컨설팅',
            'unit' => '시간',
            'purchase_price' => 0,
            'selling_price' => 300000,
            'is_active' => true,
            'is_stockable' => false,
        ]);

        // ══════════════════════════════════════
        // HW 카테고리 (최상위)
        // ══════════════════════════════════════
        $hw = ProductCategory::create([
            'name' => 'HW (하드웨어)',
            'code' => 'HW',
            'description' => '서버, 워크스테이션 및 부품',
            'is_active' => true,
            'sort_order' => 2,
        ]);

        $hwServer = ProductCategory::create([
            'name' => '서버',
            'code' => 'HW-SRV',
            'description' => '랙 마운트 / 타워 서버',
            'parent_id' => $hw->id,
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $hwWorkstation = ProductCategory::create([
            'name' => '워크스테이션',
            'code' => 'HW-WS',
            'description' => '고성능 워크스테이션',
            'parent_id' => $hw->id,
            'is_active' => true,
            'sort_order' => 2,
        ]);

        $hwParts = ProductCategory::create([
            'name' => '부품/컴포넌트',
            'code' => 'HW-PARTS',
            'description' => 'CPU, 메모리, 스토리지 등 부품',
            'parent_id' => $hw->id,
            'is_active' => true,
            'sort_order' => 3,
        ]);

        $hwAccessory = ProductCategory::create([
            'name' => '주변기기/액세서리',
            'code' => 'HW-ACC',
            'description' => '모니터, 키보드, 네트워크 장비 등',
            'parent_id' => $hw->id,
            'is_active' => true,
            'sort_order' => 4,
        ]);

        // HW 서버 상품
        $products['srv_1u'] = Product::create([
            'code' => 'HW-S-001',
            'name' => 'TW-R1000 1U 랙서버',
            'category_id' => $hwServer->id,
            'description' => '1U 랙마운트 서버 - Intel Xeon E-2400, 64GB DDR5, 2TB NVMe',
            'unit' => '대',
            'purchase_price' => 4500000,
            'selling_price' => 6800000,
            'min_stock' => 2,
            'max_stock' => 10,
            'is_active' => true,
            'is_stockable' => true,
        ]);

        $products['srv_2u'] = Product::create([
            'code' => 'HW-S-002',
            'name' => 'TW-R2000 2U 랙서버',
            'category_id' => $hwServer->id,
            'description' => '2U 랙마운트 서버 - Dual Xeon Gold 6400, 256GB DDR5, 8TB NVMe RAID',
            'unit' => '대',
            'purchase_price' => 12000000,
            'selling_price' => 18000000,
            'min_stock' => 1,
            'max_stock' => 5,
            'is_active' => true,
            'is_stockable' => true,
        ]);

        $products['srv_tower'] = Product::create([
            'code' => 'HW-S-003',
            'name' => 'TW-T500 타워서버',
            'category_id' => $hwServer->id,
            'description' => '타워 서버 - Intel Xeon W-3400, 128GB DDR5, 4TB NVMe',
            'unit' => '대',
            'purchase_price' => 7000000,
            'selling_price' => 10500000,
            'min_stock' => 2,
            'max_stock' => 8,
            'is_active' => true,
            'is_stockable' => true,
        ]);

        $products['srv_gpu'] = Product::create([
            'code' => 'HW-S-004',
            'name' => 'TW-G4000 GPU 서버',
            'category_id' => $hwServer->id,
            'description' => 'AI/ML 학습용 GPU 서버 - 4x NVIDIA A100, AMD EPYC 9004, 512GB DDR5',
            'unit' => '대',
            'purchase_price' => 80000000,
            'selling_price' => 120000000,
            'min_stock' => 0,
            'max_stock' => 3,
            'is_active' => true,
            'is_stockable' => true,
        ]);

        // HW 워크스테이션 상품
        $products['ws_desktop'] = Product::create([
            'code' => 'HW-W-001',
            'name' => 'TW-WS300 데스크탑 워크스테이션',
            'category_id' => $hwWorkstation->id,
            'description' => '데스크탑 워크스테이션 - Intel i9-14900K, RTX 4090, 128GB DDR5, 4TB NVMe',
            'unit' => '대',
            'purchase_price' => 5500000,
            'selling_price' => 8200000,
            'min_stock' => 2,
            'max_stock' => 10,
            'is_active' => true,
            'is_stockable' => true,
        ]);

        $products['ws_mobile'] = Product::create([
            'code' => 'HW-W-002',
            'name' => 'TW-WS150 모바일 워크스테이션',
            'category_id' => $hwWorkstation->id,
            'description' => '모바일 워크스테이션 (노트북) - Intel i9-14900HX, RTX 4080, 64GB DDR5, 2TB NVMe',
            'unit' => '대',
            'purchase_price' => 3800000,
            'selling_price' => 5500000,
            'min_stock' => 3,
            'max_stock' => 15,
            'is_active' => true,
            'is_stockable' => true,
        ]);

        $products['ws_ai'] = Product::create([
            'code' => 'HW-W-003',
            'name' => 'TW-WS500 AI 워크스테이션',
            'category_id' => $hwWorkstation->id,
            'description' => 'AI 개발 전용 워크스테이션 - AMD Threadripper, 2x RTX 4090, 256GB DDR5',
            'unit' => '대',
            'purchase_price' => 15000000,
            'selling_price' => 22000000,
            'min_stock' => 1,
            'max_stock' => 5,
            'is_active' => true,
            'is_stockable' => true,
        ]);

        // HW 부품 상품
        $products['cpu_xeon'] = Product::create([
            'code' => 'HW-P-001',
            'name' => 'Intel Xeon Gold 6438Y+',
            'category_id' => $hwParts->id,
            'description' => 'Intel Xeon Gold 6438Y+ 2.0GHz 32코어 60MB 캐시',
            'unit' => '개',
            'purchase_price' => 2800000,
            'selling_price' => 3500000,
            'min_stock' => 3,
            'max_stock' => 20,
            'is_active' => true,
            'is_stockable' => true,
        ]);

        $products['ram_ddr5'] = Product::create([
            'code' => 'HW-P-002',
            'name' => 'DDR5 ECC RDIMM 64GB',
            'category_id' => $hwParts->id,
            'description' => 'Samsung DDR5-5600 ECC Registered DIMM 64GB',
            'unit' => '개',
            'purchase_price' => 350000,
            'selling_price' => 480000,
            'min_stock' => 10,
            'max_stock' => 50,
            'is_active' => true,
            'is_stockable' => true,
        ]);

        $products['ssd_nvme'] = Product::create([
            'code' => 'HW-P-003',
            'name' => 'NVMe SSD 2TB (서버용)',
            'category_id' => $hwParts->id,
            'description' => 'Samsung PM9A3 2TB NVMe U.2 SSD (서버/엔터프라이즈급)',
            'unit' => '개',
            'purchase_price' => 450000,
            'selling_price' => 620000,
            'min_stock' => 5,
            'max_stock' => 30,
            'is_active' => true,
            'is_stockable' => true,
        ]);

        $products['gpu_a100'] = Product::create([
            'code' => 'HW-P-004',
            'name' => 'NVIDIA A100 80GB PCIe',
            'category_id' => $hwParts->id,
            'description' => 'NVIDIA A100 Tensor Core GPU 80GB HBM2e PCIe',
            'unit' => '개',
            'purchase_price' => 18000000,
            'selling_price' => 22000000,
            'min_stock' => 1,
            'max_stock' => 10,
            'is_active' => true,
            'is_stockable' => true,
        ]);

        $products['psu_1200'] = Product::create([
            'code' => 'HW-P-005',
            'name' => '서버용 PSU 1200W',
            'category_id' => $hwParts->id,
            'description' => '서버용 이중화 파워서플라이 1200W 80+ Titanium',
            'unit' => '개',
            'purchase_price' => 280000,
            'selling_price' => 380000,
            'min_stock' => 5,
            'max_stock' => 20,
            'is_active' => true,
            'is_stockable' => true,
        ]);

        $products['motherboard_srv'] = Product::create([
            'code' => 'HW-P-006',
            'name' => '서버 메인보드 (Dual Socket)',
            'category_id' => $hwParts->id,
            'description' => 'Supermicro Dual Socket LGA 4677 서버 메인보드',
            'unit' => '개',
            'purchase_price' => 1200000,
            'selling_price' => 1600000,
            'min_stock' => 3,
            'max_stock' => 15,
            'is_active' => true,
            'is_stockable' => true,
        ]);

        // HW 주변기기/액세서리
        $products['monitor'] = Product::create([
            'code' => 'HW-A-001',
            'name' => '모니터 27인치 4K',
            'category_id' => $hwAccessory->id,
            'description' => 'Dell UltraSharp 27인치 4K USB-C 모니터',
            'unit' => '대',
            'purchase_price' => 650000,
            'selling_price' => 850000,
            'min_stock' => 5,
            'max_stock' => 20,
            'is_active' => true,
            'is_stockable' => true,
        ]);

        $products['keyboard'] = Product::create([
            'code' => 'HW-A-002',
            'name' => '기계식 키보드',
            'category_id' => $hwAccessory->id,
            'description' => '레오폴드 FC660M 기계식 키보드 (저소음 적축)',
            'unit' => '개',
            'purchase_price' => 150000,
            'selling_price' => 200000,
            'min_stock' => 5,
            'max_stock' => 20,
            'is_active' => true,
            'is_stockable' => true,
        ]);

        $products['switch_10g'] = Product::create([
            'code' => 'HW-A-003',
            'name' => '10G 네트워크 스위치',
            'category_id' => $hwAccessory->id,
            'description' => 'Mikrotik 10G 24포트 관리형 스위치',
            'unit' => '대',
            'purchase_price' => 1800000,
            'selling_price' => 2500000,
            'min_stock' => 1,
            'max_stock' => 5,
            'is_active' => true,
            'is_stockable' => true,
        ]);

        $products['ups'] = Product::create([
            'code' => 'HW-A-004',
            'name' => '랙마운트 UPS 3kVA',
            'category_id' => $hwAccessory->id,
            'description' => 'APC Smart-UPS 3000VA 2U 랙마운트형 UPS',
            'unit' => '대',
            'purchase_price' => 1500000,
            'selling_price' => 2100000,
            'min_stock' => 1,
            'max_stock' => 5,
            'is_active' => true,
            'is_stockable' => true,
        ]);

        return $products;
    }

    // ─────────────────────────────────────────────
    // 5. 공급업체 생성
    // ─────────────────────────────────────────────
    private function createSuppliers(): array
    {
        $suppliers = [];

        $suppliers['server_vendor'] = Supplier::create([
            'company_name' => '(주)서버코리아',
            'code' => 'SUP-001',
            'business_number' => '123-45-67890',
            'representative' => '김서버',
            'contact_name' => '박영업',
            'phone' => '02-555-1234',
            'email' => 'sales@serverkorea.co.kr',
            'address' => '서울시 용산구 전자상가로 100',
            'bank_name' => '국민은행',
            'bank_account' => '123-456-789012',
            'bank_holder' => '(주)서버코리아',
            'status' => '활성',
            'payment_terms' => '정산',
            'payment_days' => 30,
            'note' => '서버/워크스테이션 주거래 업체',
        ]);

        $suppliers['parts_vendor'] = Supplier::create([
            'company_name' => '인텔코리아(주)',
            'code' => 'SUP-002',
            'business_number' => '234-56-78901',
            'representative' => '이인텔',
            'contact_name' => '최공급',
            'phone' => '02-777-5678',
            'email' => 'supply@intelkorea.co.kr',
            'address' => '서울시 서초구 반포대로 200',
            'bank_name' => '신한은행',
            'bank_account' => '110-234-567890',
            'bank_holder' => '인텔코리아(주)',
            'status' => '활성',
            'payment_terms' => '후불',
            'payment_days' => 15,
            'note' => 'CPU, 메인보드 등 부품 공급',
        ]);

        $suppliers['gpu_vendor'] = Supplier::create([
            'company_name' => '엔비디아파트너스(주)',
            'code' => 'SUP-003',
            'business_number' => '345-67-89012',
            'representative' => '정엔비',
            'contact_name' => '한지피',
            'phone' => '02-888-9999',
            'email' => 'partner@nvidiapartners.kr',
            'address' => '서울시 강남구 역삼동 789',
            'bank_name' => '우리은행',
            'bank_account' => '1002-345-678901',
            'bank_holder' => '엔비디아파트너스(주)',
            'status' => '활성',
            'payment_terms' => '선불',
            'payment_days' => 0,
            'note' => 'GPU 및 AI 가속기 공급',
        ]);

        $suppliers['cloud_vendor'] = Supplier::create([
            'company_name' => 'AWS코리아(주)',
            'code' => 'SUP-004',
            'business_number' => '456-78-90123',
            'representative' => '오클라',
            'contact_name' => '강서비',
            'phone' => '1544-5678',
            'email' => 'support@awskorea.kr',
            'address' => '서울시 강남구 봉은사로 524',
            'bank_name' => '하나은행',
            'bank_account' => '267-890-123456',
            'bank_holder' => 'AWS코리아(주)',
            'status' => '활성',
            'payment_terms' => '후불',
            'payment_days' => 30,
            'note' => '클라우드 인프라 (EC2, S3, RDS 등)',
        ]);

        $suppliers['office_vendor'] = Supplier::create([
            'company_name' => '오피스디포코리아',
            'code' => 'SUP-005',
            'business_number' => '567-89-01234',
            'representative' => '윤오피',
            'contact_name' => '문담당',
            'phone' => '1588-1234',
            'email' => 'order@officedepot.kr',
            'address' => '서울시 성동구 성수동 234-5',
            'bank_name' => 'KB국민은행',
            'bank_account' => '468-21-0123-456',
            'bank_holder' => '오피스디포코리아',
            'status' => '활성',
            'payment_terms' => '선불',
            'payment_days' => 0,
            'note' => '사무용품, 주변기기 공급',
        ]);

        return $suppliers;
    }

    // ─────────────────────────────────────────────
    // 6. 고객사 및 담당자 생성
    // ─────────────────────────────────────────────
    private function createCustomersAndContacts(array $users): array
    {
        $customers = [];

        // 고객 1 - 게임사 (VIP, SW 개발 + AI)
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
            'assigned_to' => $users['emb_lead']->id,
            'note' => '게임 백오피스 시스템 개발 + AI 챗봇 프로젝트',
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

        // 고객 2 - 핀테크 (SW API 연동)
        $customers['fintech'] = Customer::create([
            'company_name' => '핀테크솔루션(주)',
            'business_number' => '222-33-44444',
            'representative' => '최핀테',
            'industry' => 'IT/금융',
            'business_type' => '핀테크 솔루션',
            'phone' => '02-3333-4444',
            'email' => 'contact@fintechsol.kr',
            'website' => 'https://www.fintechsol.kr',
            'address' => '서울시 영등포구 여의도동 35',
            'type' => '고객',
            'status' => '활성',
            'assigned_to' => $users['emb_lead']->id,
            'note' => '결제 API 연동 프로젝트',
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

        // 고객 3 - 제조사 (HW 서버 + SW 구축)
        $customers['manufacturing'] = Customer::create([
            'company_name' => '(주)코리아매뉴팩처링',
            'business_number' => '333-44-55555',
            'representative' => '오제조',
            'industry' => '제조업',
            'business_type' => '스마트팩토리 솔루션',
            'phone' => '031-555-6666',
            'email' => 'info@koreamfg.kr',
            'website' => 'https://www.koreamfg.kr',
            'address' => '경기도 화성시 동탄산업단지로 100',
            'type' => 'VIP',
            'status' => '활성',
            'assigned_to' => $users['emb_lead']->id,
            'note' => '스마트팩토리 서버 구축 + IoT 펌웨어 개발',
        ]);

        Contact::create([
            'customer_id' => $customers['manufacturing']->id,
            'name' => '강공장',
            'position' => '부장',
            'department' => '스마트팩토리팀',
            'phone' => '031-555-6667',
            'mobile' => '010-6666-7777',
            'email' => 'factory@koreamfg.kr',
            'is_primary' => true,
        ]);

        Contact::create([
            'customer_id' => $customers['manufacturing']->id,
            'name' => '윤구매',
            'position' => '과장',
            'department' => '구매팀',
            'phone' => '031-555-6668',
            'mobile' => '010-7777-8888',
            'email' => 'purchase@koreamfg.kr',
            'is_primary' => false,
        ]);

        // 고객 4 - 대학/연구소 (AI GPU 서버)
        $customers['university'] = Customer::create([
            'company_name' => '한국과학기술원 (KAIST)',
            'business_number' => '444-55-66666',
            'representative' => '한연구',
            'industry' => '교육/연구',
            'business_type' => '대학/연구기관',
            'phone' => '042-350-2000',
            'email' => 'procurement@kaist.ac.kr',
            'website' => 'https://www.kaist.ac.kr',
            'address' => '대전시 유성구 대학로 291',
            'type' => '고객',
            'status' => '활성',
            'assigned_to' => $users['emb_lead']->id,
            'note' => 'AI 연구용 GPU 서버 클러스터 납품',
        ]);

        Contact::create([
            'customer_id' => $customers['university']->id,
            'name' => '송교수',
            'position' => '교수',
            'department' => 'AI대학원',
            'phone' => '042-350-3456',
            'mobile' => '010-8888-9999',
            'email' => 'prof.song@kaist.ac.kr',
            'is_primary' => true,
        ]);

        // 고객 5 - 유지보수 고객
        $customers['ecommerce'] = Customer::create([
            'company_name' => '쇼핑몰플러스(주)',
            'business_number' => '555-66-77777',
            'representative' => '한쇼핑',
            'industry' => '이커머스',
            'business_type' => '온라인 쇼핑몰 운영',
            'phone' => '02-7777-8888',
            'email' => 'admin@shoppingplus.kr',
            'website' => 'https://www.shoppingplus.kr',
            'address' => '서울시 마포구 상암동 1234',
            'type' => 'VIP',
            'status' => '활성',
            'assigned_to' => $users['emb_lead']->id,
            'note' => '쇼핑몰 시스템 유지보수 계약',
        ]);

        Contact::create([
            'customer_id' => $customers['ecommerce']->id,
            'name' => '윤운영',
            'position' => '매니저',
            'department' => '운영팀',
            'phone' => '02-7777-8889',
            'mobile' => '010-9999-0000',
            'email' => 'operation@shoppingplus.kr',
            'is_primary' => true,
        ]);

        return $customers;
    }

    // ─────────────────────────────────────────────
    // 7. 리드 생성
    // ─────────────────────────────────────────────
    private function createLeads(array $users): void
    {
        Lead::create([
            'company_name' => '(주)블루헬스',
            'contact_name' => '임건강',
            'email' => 'contact@bluehealth.kr',
            'phone' => '02-1234-5555',
            'source' => '웹사이트',
            'status' => '신규',
            'description' => '디지털 헬스케어 플랫폼 구축 문의. AI 진단 기능 포함.',
            'expected_revenue' => 60000000,
            'assigned_to' => $users['emb_lead']->id,
        ]);

        Lead::create([
            'company_name' => '에듀테크스타트업',
            'contact_name' => '김교육',
            'email' => 'ceo@edutech-startup.kr',
            'phone' => '010-5555-6666',
            'source' => '소개',
            'status' => '연락중',
            'description' => 'AI 기반 학습 플랫폼 개발 의뢰. STT/LLM 활용.',
            'expected_revenue' => 45000000,
            'assigned_to' => $users['ai_lead']->id,
        ]);

        Lead::create([
            'company_name' => '(주)스마트로봇',
            'contact_name' => '정로봇',
            'email' => 'info@smartrobot.co.kr',
            'phone' => '031-999-1234',
            'source' => '전시회',
            'status' => '제안중',
            'description' => '산업용 로봇 제어 보드 펌웨어 개발 및 서버 시스템 구축.',
            'expected_revenue' => 80000000,
            'assigned_to' => $users['emb_lead']->id,
        ]);

        Lead::create([
            'company_name' => '글로벌커머스(주)',
            'contact_name' => '송해외',
            'email' => 'bizdev@globalcommerce.kr',
            'phone' => '02-8888-1111',
            'source' => '웹사이트',
            'status' => '보류',
            'description' => '글로벌 이커머스 플랫폼 마이그레이션 문의.',
            'expected_revenue' => 200000000,
            'assigned_to' => $users['dev_lead']->id,
        ]);
    }

    // ─────────────────────────────────────────────
    // 8. 영업 기회 생성
    // ─────────────────────────────────────────────
    private function createOpportunities(array $customers, array $users): void
    {
        $baseDate = now();

        Opportunity::create([
            'name' => '게임 백오피스 2차 개발',
            'customer_id' => $customers['nexon']->id,
            'contact_id' => Contact::where('customer_id', $customers['nexon']->id)->first()->id,
            'amount' => 50000000,
            'stage' => '제안',
            'probability' => 60,
            'expected_close_date' => $baseDate->copy()->addDays(30),
            'description' => '게임 운영 백오피스 2차 기능 추가 - 실시간 대시보드, 유저 분석.',
            'assigned_to' => $users['emb_lead']->id,
            'next_step' => '제안서 발표 일정 조율',
        ]);

        Opportunity::create([
            'name' => '스마트팩토리 IoT 시스템',
            'customer_id' => $customers['manufacturing']->id,
            'contact_id' => Contact::where('customer_id', $customers['manufacturing']->id)->first()->id,
            'amount' => 150000000,
            'stage' => '협상',
            'probability' => 75,
            'expected_close_date' => $baseDate->copy()->addDays(14),
            'description' => '공장 라인 센서 데이터 수집 서버 + IoT 펌웨어 + 모니터링 대시보드.',
            'assigned_to' => $users['emb_lead']->id,
            'next_step' => '최종 견적 협의',
        ]);

        Opportunity::create([
            'name' => 'AI 연구 GPU 클러스터 2차',
            'customer_id' => $customers['university']->id,
            'contact_id' => Contact::where('customer_id', $customers['university']->id)->first()->id,
            'amount' => 500000000,
            'stage' => '접촉',
            'probability' => 30,
            'expected_close_date' => $baseDate->copy()->addDays(90),
            'description' => 'KAIST AI대학원 GPU 서버 클러스터 증설. A100 x 16 노드.',
            'assigned_to' => $users['hd_head']->id,
            'next_step' => '교수님 미팅 일정 잡기',
        ]);

        Opportunity::create([
            'name' => '모바일 뱅킹 앱 개발',
            'customer_id' => $customers['fintech']->id,
            'contact_id' => Contact::where('customer_id', $customers['fintech']->id)->first()->id,
            'amount' => 120000000,
            'stage' => '발굴',
            'probability' => 20,
            'expected_close_date' => $baseDate->copy()->addDays(60),
            'description' => '모바일 뱅킹 앱 신규 개발 프로젝트 검토 중.',
            'assigned_to' => $users['dev_lead']->id,
            'next_step' => '내부 니즈 파악',
        ]);

        // 성공 사례
        Opportunity::create([
            'name' => '결제 시스템 API 연동',
            'customer_id' => $customers['fintech']->id,
            'contact_id' => Contact::where('customer_id', $customers['fintech']->id)->first()->id,
            'amount' => 35000000,
            'stage' => '계약완료',
            'probability' => 100,
            'expected_close_date' => $baseDate->copy()->subDays(30),
            'actual_close_date' => $baseDate->copy()->subDays(25),
            'description' => '핀테크솔루션 결제 시스템 API 연동. 성공적으로 계약.',
            'assigned_to' => $users['dev_lead']->id,
            'next_step' => null,
        ]);

        // 실패 사례
        Opportunity::create([
            'name' => '레거시 시스템 마이그레이션',
            'customer_id' => $customers['nexon']->id,
            'contact_id' => Contact::where('customer_id', $customers['nexon']->id)->first()->id,
            'amount' => 70000000,
            'stage' => '실패',
            'probability' => 0,
            'expected_close_date' => $baseDate->copy()->subDays(15),
            'actual_close_date' => $baseDate->copy()->subDays(10),
            'description' => '레거시 시스템 마이그레이션. 고객사 내부 사정으로 무기한 보류.',
            'assigned_to' => $users['dev_lead']->id,
            'next_step' => null,
        ]);
    }

    // ─────────────────────────────────────────────
    // 9. 계약 생성
    // ─────────────────────────────────────────────
    private function createContracts(array $customers, array $users): array
    {
        $contracts = [];
        $baseDate = now();

        $contracts['nexon'] = Contract::create([
            'contract_number' => 'CT-' . $baseDate->copy()->subDays(25)->format('Ymd') . '-0001',
            'title' => '게임 백오피스 시스템 개발',
            'customer_id' => $customers['nexon']->id,
            'start_date' => $baseDate->copy()->subDays(20),
            'end_date' => $baseDate->copy()->addMonths(3),
            'amount' => 80000000,
            'status' => '진행중',
            'payment_terms' => '분할',
            'description' => '게임 운영용 백오피스 관리 시스템 개발 (착수금 30%, 중도금 40%, 잔금 30%)',
            'signed_by' => $users['ceo']->id,
            'signed_at' => $baseDate->copy()->subDays(25),
        ]);

        $contracts['fintech'] = Contract::create([
            'contract_number' => 'CT-' . $baseDate->copy()->subDays(15)->format('Ymd') . '-0002',
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

        $contracts['manufacturing'] = Contract::create([
            'contract_number' => 'CT-' . $baseDate->copy()->subDays(10)->format('Ymd') . '-0003',
            'title' => '스마트팩토리 서버 인프라 구축',
            'customer_id' => $customers['manufacturing']->id,
            'start_date' => $baseDate->copy()->subDays(5),
            'end_date' => $baseDate->copy()->addMonths(4),
            'amount' => 120000000,
            'status' => '진행중',
            'payment_terms' => '분할',
            'description' => '공장 서버 인프라 + IoT 데이터 수집 시스템 구축 (착수금 30%, 중도금 40%, 잔금 30%)',
            'signed_by' => $users['ceo']->id,
            'signed_at' => $baseDate->copy()->subDays(10),
        ]);

        $contracts['university'] = Contract::create([
            'contract_number' => 'CT-' . $baseDate->copy()->subDays(20)->format('Ymd') . '-0004',
            'title' => 'AI 연구용 GPU 서버 납품',
            'customer_id' => $customers['university']->id,
            'start_date' => $baseDate->copy()->subDays(15),
            'end_date' => $baseDate->copy()->addMonths(1),
            'amount' => 250000000,
            'status' => '진행중',
            'payment_terms' => '후불',
            'description' => 'GPU 서버 4대 (A100 x 4 구성) 납품 및 설치',
            'signed_by' => $users['ceo']->id,
            'signed_at' => $baseDate->copy()->subDays(20),
        ]);

        $contracts['ecommerce'] = Contract::create([
            'contract_number' => 'CT-' . $baseDate->copy()->subMonths(1)->format('Ymd') . '-0005',
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

    // ─────────────────────────────────────────────
    // 10. 프로젝트 생성
    // ─────────────────────────────────────────────
    private function createProjects(array $customers, array $contracts, array $users): array
    {
        $projects = [];
        $baseDate = now();

        // SW 프로젝트 - 게임 백오피스
        $projects['nexon'] = Project::create([
            'code' => 'PRJ-' . $baseDate->copy()->subDays(20)->format('Ymd') . '-0001',
            'name' => '게임 백오피스 시스템',
            'description' => '넥스트게임즈 게임 운영용 종합 백오피스 관리 시스템 개발',
            'customer_id' => $customers['nexon']->id,
            'contract_id' => $contracts['nexon']->id,
            'manager_id' => $users['dev_lead']->id,
            'start_date' => $baseDate->copy()->subDays(20),
            'end_date' => $baseDate->copy()->addMonths(3),
            'budget' => 80000000,
            'actual_cost' => 12000000,
            'status' => '진행중',
            'progress' => 25,
            'priority' => '높음',
        ]);

        // SW 프로젝트 - 결제 API
        $projects['fintech'] = Project::create([
            'code' => 'PRJ-' . $baseDate->copy()->subDays(10)->format('Ymd') . '-0002',
            'name' => '결제 API 연동',
            'description' => 'PG사 결제 API 연동 및 커스터마이징 개발',
            'customer_id' => $customers['fintech']->id,
            'contract_id' => $contracts['fintech']->id,
            'manager_id' => $users['dev_lead']->id,
            'start_date' => $baseDate->copy()->subDays(10),
            'end_date' => $baseDate->copy()->addMonths(2),
            'budget' => 35000000,
            'actual_cost' => 5000000,
            'status' => '진행중',
            'progress' => 15,
            'priority' => '높음',
        ]);

        // HW + SW 프로젝트 - 스마트팩토리
        $projects['manufacturing'] = Project::create([
            'code' => 'PRJ-' . $baseDate->copy()->subDays(5)->format('Ymd') . '-0003',
            'name' => '스마트팩토리 서버 인프라 구축',
            'description' => '코리아매뉴팩처링 공장 서버 인프라 + IoT 데이터 수집 시스템',
            'customer_id' => $customers['manufacturing']->id,
            'contract_id' => $contracts['manufacturing']->id,
            'manager_id' => $users['hw_lead']->id,
            'start_date' => $baseDate->copy()->subDays(5),
            'end_date' => $baseDate->copy()->addMonths(4),
            'budget' => 120000000,
            'actual_cost' => 3000000,
            'status' => '진행중',
            'progress' => 5,
            'priority' => '높음',
        ]);

        // HW 프로젝트 - GPU 서버 납품
        $projects['university'] = Project::create([
            'code' => 'PRJ-' . $baseDate->copy()->subDays(15)->format('Ymd') . '-0004',
            'name' => 'AI 연구 GPU 서버 클러스터',
            'description' => 'KAIST AI대학원 GPU 서버 4대 (A100 x 4) 납품 및 설치',
            'customer_id' => $customers['university']->id,
            'contract_id' => $contracts['university']->id,
            'manager_id' => $users['hd_head']->id,
            'start_date' => $baseDate->copy()->subDays(15),
            'end_date' => $baseDate->copy()->addMonths(1),
            'budget' => 250000000,
            'actual_cost' => 180000000,
            'status' => '진행중',
            'progress' => 60,
            'priority' => '높음',
        ]);

        // 유지보수 프로젝트
        $projects['ecommerce'] = Project::create([
            'code' => 'PRJ-' . $baseDate->copy()->subMonths(1)->format('Ymd') . '-0005',
            'name' => '쇼핑몰 시스템 유지보수',
            'description' => '쇼핑몰플러스 시스템 운영 및 유지보수',
            'customer_id' => $customers['ecommerce']->id,
            'contract_id' => $contracts['ecommerce']->id,
            'manager_id' => $users['dev_lead']->id,
            'start_date' => $baseDate->copy()->subMonths(1),
            'end_date' => $baseDate->copy()->addMonths(11),
            'budget' => 36000000,
            'actual_cost' => 3000000,
            'status' => '진행중',
            'progress' => 8,
            'priority' => '보통',
        ]);

        // 프로젝트 멤버 배정
        $projects['nexon']->members()->attach([
            $users['dev_lead']->id => ['role' => 'PM', 'joined_at' => now()->subDays(20)],
            $users['dev_member']->id => ['role' => '개발자', 'joined_at' => now()->subDays(20)],
            $users['ai_member']->id => ['role' => '개발자', 'joined_at' => now()->subDays(15)],
        ]);

        $projects['fintech']->members()->attach([
            $users['dev_lead']->id => ['role' => 'PM', 'joined_at' => now()->subDays(10)],
            $users['dev_member']->id => ['role' => '개발자', 'joined_at' => now()->subDays(10)],
        ]);

        $projects['manufacturing']->members()->attach([
            $users['hw_lead']->id => ['role' => 'PM', 'joined_at' => now()->subDays(5)],
            $users['hw_member']->id => ['role' => '설계', 'joined_at' => now()->subDays(5)],
            $users['emb_lead']->id => ['role' => 'FW개발', 'joined_at' => now()->subDays(5)],
            $users['emb_member']->id => ['role' => 'FW개발', 'joined_at' => now()->subDays(5)],
        ]);

        $projects['university']->members()->attach([
            $users['hd_head']->id => ['role' => 'PM', 'joined_at' => now()->subDays(15)],
            $users['hw_lead']->id => ['role' => '설계', 'joined_at' => now()->subDays(15)],
            $users['hw_member']->id => ['role' => '조립/테스트', 'joined_at' => now()->subDays(15)],
        ]);

        $projects['ecommerce']->members()->attach([
            $users['dev_lead']->id => ['role' => 'PM', 'joined_at' => now()->subMonths(1)],
            $users['dev_member']->id => ['role' => '개발자', 'joined_at' => now()->subMonths(1)],
        ]);

        return $projects;
    }

    // ─────────────────────────────────────────────
    // 11. 마일스톤 및 태스크 생성
    // ─────────────────────────────────────────────
    private function createMilestonesAndTasks(array $projects, array $users): void
    {
        $baseDate = now();

        // ── 프로젝트 1: 게임 백오피스 ──
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
            'title' => '고객 요구사항 인터뷰',
            'description' => '넥스트게임즈 담당자 인터뷰 및 요구사항 수집',
            'assigned_to' => $users['dev_lead']->id,
            'created_by' => $users['dev_lead']->id,
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
            'description' => 'ERD, 시스템 아키텍처 설계, API 스펙 정의',
            'assigned_to' => $users['dev_lead']->id,
            'created_by' => $users['dev_lead']->id,
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
            'description' => 'MySQL 테이블 설계 및 마이그레이션 작성',
            'assigned_to' => $users['dev_member']->id,
            'created_by' => $users['dev_lead']->id,
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
            'description' => '회원, 권한 관리 CRUD API 구현',
            'assigned_to' => $users['dev_member']->id,
            'created_by' => $users['dev_lead']->id,
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
            'description' => '게임 아이템, 캐릭터, 이벤트 관리 API',
            'assigned_to' => $users['ai_member']->id,
            'created_by' => $users['dev_lead']->id,
            'status' => '할일',
            'priority' => '보통',
            'start_date' => $baseDate->copy()->addDays(5),
            'due_date' => $baseDate->copy()->addDays(15),
            'estimated_hours' => 48,
        ]);

        $m3 = Milestone::create([
            'project_id' => $projects['nexon']->id,
            'name' => '3단계: 프론트엔드 개발',
            'description' => '관리자 UI 개발',
            'due_date' => $baseDate->copy()->addDays(45),
            'status' => '대기',
            'sort_order' => 3,
        ]);

        Task::create([
            'project_id' => $projects['nexon']->id,
            'milestone_id' => $m3->id,
            'title' => '대시보드 UI 구현',
            'description' => '실시간 게임 통계 대시보드 프론트엔드',
            'assigned_to' => $users['dev_member']->id,
            'created_by' => $users['dev_lead']->id,
            'status' => '할일',
            'priority' => '보통',
            'start_date' => $baseDate->copy()->addDays(15),
            'due_date' => $baseDate->copy()->addDays(30),
            'estimated_hours' => 60,
        ]);

        // ── 프로젝트 2: 결제 API ──
        $m4 = Milestone::create([
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
            'milestone_id' => $m4->id,
            'title' => 'PG사 API 문서 분석',
            'description' => '토스페이먼츠 API 분석 및 연동 방안 수립',
            'assigned_to' => $users['dev_lead']->id,
            'created_by' => $users['dev_lead']->id,
            'status' => '완료',
            'priority' => '높음',
            'start_date' => $baseDate->copy()->subDays(10),
            'due_date' => $baseDate->copy()->subDays(5),
            'completed_date' => $baseDate->copy()->subDays(6),
            'estimated_hours' => 8,
            'actual_hours' => 6,
        ]);

        $m5 = Milestone::create([
            'project_id' => $projects['fintech']->id,
            'name' => 'API 개발 및 테스트',
            'description' => '결제 API 개발 및 통합 테스트',
            'due_date' => $baseDate->copy()->addDays(30),
            'status' => '진행중',
            'sort_order' => 2,
        ]);

        Task::create([
            'project_id' => $projects['fintech']->id,
            'milestone_id' => $m5->id,
            'title' => '결제 요청 API 개발',
            'description' => '카드 결제, 간편결제 API 구현',
            'assigned_to' => $users['dev_member']->id,
            'created_by' => $users['dev_lead']->id,
            'status' => '진행중',
            'priority' => '높음',
            'start_date' => $baseDate->copy()->subDays(2),
            'due_date' => $baseDate->copy()->addDays(10),
            'estimated_hours' => 32,
            'actual_hours' => 8,
        ]);

        // ── 프로젝트 3: 스마트팩토리 ──
        $m6 = Milestone::create([
            'project_id' => $projects['manufacturing']->id,
            'name' => '서버 하드웨어 설계',
            'description' => '공장 환경 맞춤 서버 하드웨어 구성 설계',
            'due_date' => $baseDate->copy()->addDays(15),
            'status' => '진행중',
            'sort_order' => 1,
        ]);

        Task::create([
            'project_id' => $projects['manufacturing']->id,
            'milestone_id' => $m6->id,
            'title' => '서버 스펙 설계',
            'description' => '공장 환경 조건 분석 및 서버 하드웨어 스펙 결정',
            'assigned_to' => $users['hw_lead']->id,
            'created_by' => $users['hw_lead']->id,
            'status' => '진행중',
            'priority' => '높음',
            'start_date' => $baseDate->copy()->subDays(5),
            'due_date' => $baseDate->copy()->addDays(5),
            'estimated_hours' => 24,
            'actual_hours' => 8,
        ]);

        Task::create([
            'project_id' => $projects['manufacturing']->id,
            'milestone_id' => $m6->id,
            'title' => 'PCB 센서 보드 설계',
            'description' => 'IoT 센서 데이터 수집용 커스텀 PCB 설계',
            'assigned_to' => $users['hw_member']->id,
            'created_by' => $users['hw_lead']->id,
            'status' => '할일',
            'priority' => '높음',
            'start_date' => $baseDate->copy()->addDays(3),
            'due_date' => $baseDate->copy()->addDays(15),
            'estimated_hours' => 40,
        ]);

        $m7 = Milestone::create([
            'project_id' => $projects['manufacturing']->id,
            'name' => '펌웨어 개발',
            'description' => 'IoT 센서 보드 펌웨어 개발',
            'due_date' => $baseDate->copy()->addDays(45),
            'status' => '대기',
            'sort_order' => 2,
        ]);

        Task::create([
            'project_id' => $projects['manufacturing']->id,
            'milestone_id' => $m7->id,
            'title' => '센서 펌웨어 개발',
            'description' => '온습도, 진동, 전력 센서 데이터 수집 펌웨어',
            'assigned_to' => $users['emb_lead']->id,
            'created_by' => $users['hw_lead']->id,
            'status' => '할일',
            'priority' => '높음',
            'start_date' => $baseDate->copy()->addDays(15),
            'due_date' => $baseDate->copy()->addDays(35),
            'estimated_hours' => 80,
        ]);

        Task::create([
            'project_id' => $projects['manufacturing']->id,
            'milestone_id' => $m7->id,
            'title' => '통신 프로토콜 구현',
            'description' => 'MQTT/HTTP 기반 데이터 전송 프로토콜 구현',
            'assigned_to' => $users['emb_member']->id,
            'created_by' => $users['emb_lead']->id,
            'status' => '할일',
            'priority' => '보통',
            'start_date' => $baseDate->copy()->addDays(20),
            'due_date' => $baseDate->copy()->addDays(40),
            'estimated_hours' => 60,
        ]);

        // ── 프로젝트 4: GPU 서버 클러스터 ──
        $m8 = Milestone::create([
            'project_id' => $projects['university']->id,
            'name' => '서버 조립 및 OS 설치',
            'description' => 'GPU 서버 4대 조립, 번인 테스트, OS/드라이버 설치',
            'due_date' => $baseDate->copy()->subDays(5),
            'completed_date' => $baseDate->copy()->subDays(3),
            'status' => '완료',
            'sort_order' => 1,
        ]);

        Task::create([
            'project_id' => $projects['university']->id,
            'milestone_id' => $m8->id,
            'title' => 'GPU 서버 조립 (4대)',
            'description' => 'A100 x 4 구성 서버 4대 조립 및 번인 테스트',
            'assigned_to' => $users['hw_member']->id,
            'created_by' => $users['hd_head']->id,
            'status' => '완료',
            'priority' => '높음',
            'start_date' => $baseDate->copy()->subDays(15),
            'due_date' => $baseDate->copy()->subDays(8),
            'completed_date' => $baseDate->copy()->subDays(7),
            'estimated_hours' => 32,
            'actual_hours' => 28,
        ]);

        Task::create([
            'project_id' => $projects['university']->id,
            'milestone_id' => $m8->id,
            'title' => 'OS/드라이버 설치',
            'description' => 'Ubuntu 22.04 LTS + CUDA + Docker 환경 구성',
            'assigned_to' => $users['hw_lead']->id,
            'created_by' => $users['hd_head']->id,
            'status' => '완료',
            'priority' => '높음',
            'start_date' => $baseDate->copy()->subDays(8),
            'due_date' => $baseDate->copy()->subDays(5),
            'completed_date' => $baseDate->copy()->subDays(4),
            'estimated_hours' => 16,
            'actual_hours' => 12,
        ]);

        $m9 = Milestone::create([
            'project_id' => $projects['university']->id,
            'name' => '현장 설치 및 네트워크 구성',
            'description' => 'KAIST 서버실 설치, 네트워크 구성, 최종 테스트',
            'due_date' => $baseDate->copy()->addDays(10),
            'status' => '진행중',
            'sort_order' => 2,
        ]);

        Task::create([
            'project_id' => $projects['university']->id,
            'milestone_id' => $m9->id,
            'title' => '서버실 설치 및 배선',
            'description' => 'KAIST AI대학원 서버실 랙 마운트 및 전원/네트워크 배선',
            'assigned_to' => $users['hw_member']->id,
            'created_by' => $users['hd_head']->id,
            'status' => '진행중',
            'priority' => '높음',
            'start_date' => $baseDate->copy()->subDays(2),
            'due_date' => $baseDate->copy()->addDays(3),
            'estimated_hours' => 16,
            'actual_hours' => 6,
        ]);

        Task::create([
            'project_id' => $projects['university']->id,
            'milestone_id' => $m9->id,
            'title' => '네트워크 구성 및 최종 테스트',
            'description' => 'InfiniBand 연결, GPU 클러스터 벤치마크 테스트',
            'assigned_to' => $users['hw_lead']->id,
            'created_by' => $users['hd_head']->id,
            'status' => '할일',
            'priority' => '높음',
            'start_date' => $baseDate->copy()->addDays(3),
            'due_date' => $baseDate->copy()->addDays(8),
            'estimated_hours' => 24,
        ]);

        // ── 유지보수 프로젝트 (마일스톤 없이 태스크만) ──
        Task::create([
            'project_id' => $projects['ecommerce']->id,
            'title' => '결제 모듈 버그 수정',
            'description' => '카드 결제 시 간헐적 타임아웃 이슈 수정',
            'assigned_to' => $users['dev_member']->id,
            'created_by' => $users['dev_lead']->id,
            'status' => '완료',
            'priority' => '긴급',
            'start_date' => $baseDate->copy()->subDays(7),
            'due_date' => $baseDate->copy()->subDays(5),
            'completed_date' => $baseDate->copy()->subDays(5),
            'estimated_hours' => 8,
            'actual_hours' => 6,
        ]);

        Task::create([
            'project_id' => $projects['ecommerce']->id,
            'title' => '상품 검색 성능 개선',
            'description' => 'Elasticsearch 인덱스 최적화 및 쿼리 튜닝',
            'assigned_to' => $users['dev_member']->id,
            'created_by' => $users['dev_lead']->id,
            'status' => '진행중',
            'priority' => '보통',
            'start_date' => $baseDate->copy()->subDays(3),
            'due_date' => $baseDate->copy()->addDays(5),
            'estimated_hours' => 16,
            'actual_hours' => 4,
        ]);

        Task::create([
            'project_id' => $projects['ecommerce']->id,
            'title' => '2월 정기 보안 패치',
            'description' => 'Laravel, Node.js 보안 패치 적용 및 테스트',
            'assigned_to' => $users['dev_lead']->id,
            'created_by' => $users['dev_lead']->id,
            'status' => '할일',
            'priority' => '높음',
            'start_date' => $baseDate->copy()->addDays(7),
            'due_date' => $baseDate->copy()->addDays(10),
            'estimated_hours' => 12,
        ]);
    }

    // ─────────────────────────────────────────────
    // 12. 타임시트 생성
    // ─────────────────────────────────────────────
    private function createTimesheets(array $projects, array $users): void
    {
        $baseDate = now();

        for ($i = 21; $i >= 1; $i--) {
            $date = $baseDate->copy()->subDays($i);

            if ($date->isWeekend()) {
                continue;
            }

            // 개발팀장 - 게임 백오피스
            Timesheet::create([
                'user_id' => $users['dev_lead']->id,
                'project_id' => $projects['nexon']->id,
                'date' => $date,
                'hours' => rand(6, 8),
                'description' => '게임 백오피스 개발 작업',
                'is_billable' => true,
                'hourly_rate' => 150000,
                'status' => $i > 7 ? '승인' : '대기',
                'approved_by' => $i > 7 ? $users['sd_head']->id : null,
                'approved_at' => $i > 7 ? $date->copy()->addDay() : null,
            ]);

            // 개발팀원 - 게임 백오피스
            if ($i <= 15) {
                Timesheet::create([
                    'user_id' => $users['dev_member']->id,
                    'project_id' => $projects['nexon']->id,
                    'date' => $date,
                    'hours' => rand(7, 8),
                    'description' => 'DB 스키마 구축 및 API 개발',
                    'is_billable' => true,
                    'hourly_rate' => 100000,
                    'status' => $i > 7 ? '승인' : '대기',
                    'approved_by' => $i > 7 ? $users['dev_lead']->id : null,
                    'approved_at' => $i > 7 ? $date->copy()->addDay() : null,
                ]);
            }

            // 개발팀원 - 결제 API (병행)
            if ($i <= 10) {
                Timesheet::create([
                    'user_id' => $users['dev_member']->id,
                    'project_id' => $projects['fintech']->id,
                    'date' => $date,
                    'hours' => rand(2, 4),
                    'description' => '결제 API 개발',
                    'is_billable' => true,
                    'hourly_rate' => 100000,
                    'status' => $i > 5 ? '승인' : '대기',
                    'approved_by' => $i > 5 ? $users['dev_lead']->id : null,
                    'approved_at' => $i > 5 ? $date->copy()->addDay() : null,
                ]);
            }

            // 설계팀장 - 스마트팩토리
            if ($i <= 5) {
                Timesheet::create([
                    'user_id' => $users['hw_lead']->id,
                    'project_id' => $projects['manufacturing']->id,
                    'date' => $date,
                    'hours' => rand(6, 8),
                    'description' => '서버 스펙 설계 및 부품 선정',
                    'is_billable' => true,
                    'hourly_rate' => 130000,
                    'status' => '대기',
                ]);
            }

            // 설계팀원 - GPU 서버 프로젝트
            if ($i <= 15 && $i > 5) {
                Timesheet::create([
                    'user_id' => $users['hw_member']->id,
                    'project_id' => $projects['university']->id,
                    'date' => $date,
                    'hours' => rand(7, 8),
                    'description' => 'GPU 서버 조립 및 테스트',
                    'is_billable' => true,
                    'hourly_rate' => 100000,
                    'status' => '승인',
                    'approved_by' => $users['hd_head']->id,
                    'approved_at' => $date->copy()->addDay(),
                ]);
            }
        }
    }

    // ─────────────────────────────────────────────
    // 13. 발주서 생성
    // ─────────────────────────────────────────────
    private function createPurchaseOrders(array $suppliers, array $products, array $warehouses, array $users): void
    {
        $baseDate = now();

        // 발주 1 - GPU 서버 부품 (입고 완료)
        $po1 = PurchaseOrder::create([
            'po_number' => 'PO-' . $baseDate->copy()->subDays(20)->format('Ymd') . '-0001',
            'supplier_id' => $suppliers['gpu_vendor']->id,
            'project_id' => $projects['university']->id ?? null,
            'order_date' => $baseDate->copy()->subDays(20),
            'expected_date' => $baseDate->copy()->subDays(12),
            'received_date' => $baseDate->copy()->subDays(11),
            'subtotal' => 77200000,
            'tax_amount' => 7720000,
            'total_amount' => 84920000,
            'status' => '입고완료',
            'note' => 'KAIST GPU 서버 프로젝트 - GPU 및 부품 발주',
            'shipping_address' => '서울시 강남구 테헤란로 123, 테크웨이브빌딩 B1',
            'created_by' => $users['hw_lead']->id,
            'approved_by' => $users['hd_head']->id,
            'approved_at' => $baseDate->copy()->subDays(19),
        ]);

        PurchaseOrderItem::create([
            'purchase_order_id' => $po1->id,
            'product_id' => $products['gpu_a100']->id,
            'description' => 'NVIDIA A100 80GB PCIe (서버 4대 x 4개)',
            'quantity' => 16,
            'unit' => '개',
            'unit_price' => 18000000,
            'tax_rate' => 10,
            'amount' => 288000000,
            'received_quantity' => 16,
        ]);

        // 발주 1 금액 재계산 (실제값 반영)
        $po1->update([
            'subtotal' => 288000000,
            'tax_amount' => 28800000,
            'total_amount' => 316800000,
        ]);

        // 발주 2 - 서버 부품 (입고 완료)
        $po2 = PurchaseOrder::create([
            'po_number' => 'PO-' . $baseDate->copy()->subDays(18)->format('Ymd') . '-0002',
            'supplier_id' => $suppliers['parts_vendor']->id,
            'order_date' => $baseDate->copy()->subDays(18),
            'expected_date' => $baseDate->copy()->subDays(12),
            'received_date' => $baseDate->copy()->subDays(11),
            'subtotal' => 33600000,
            'tax_amount' => 3360000,
            'total_amount' => 36960000,
            'status' => '입고완료',
            'note' => 'KAIST 서버 - CPU, RAM, SSD 발주',
            'shipping_address' => '서울시 강남구 테헤란로 123, 테크웨이브빌딩 B1',
            'created_by' => $users['hw_lead']->id,
            'approved_by' => $users['hd_head']->id,
            'approved_at' => $baseDate->copy()->subDays(17),
        ]);

        PurchaseOrderItem::create([
            'purchase_order_id' => $po2->id,
            'product_id' => $products['cpu_xeon']->id,
            'description' => 'Intel Xeon Gold 6438Y+ (서버 4대 x 2개)',
            'quantity' => 8,
            'unit' => '개',
            'unit_price' => 2800000,
            'tax_rate' => 10,
            'amount' => 22400000,
            'received_quantity' => 8,
        ]);

        PurchaseOrderItem::create([
            'purchase_order_id' => $po2->id,
            'product_id' => $products['ram_ddr5']->id,
            'description' => 'DDR5 ECC 64GB (서버 4대 x 8개)',
            'quantity' => 32,
            'unit' => '개',
            'unit_price' => 350000,
            'tax_rate' => 10,
            'amount' => 11200000,
            'received_quantity' => 32,
        ]);

        // 재고 생성 (입고된 HW 부품)
        Stock::create([
            'warehouse_id' => $warehouses['hw_lab']->id,
            'product_id' => $products['monitor']->id,
            'quantity' => 5,
            'reserved_quantity' => 0,
        ]);

        Stock::create([
            'warehouse_id' => $warehouses['hw_lab']->id,
            'product_id' => $products['keyboard']->id,
            'quantity' => 8,
            'reserved_quantity' => 0,
        ]);

        Stock::create([
            'warehouse_id' => $warehouses['hw_lab']->id,
            'product_id' => $products['ram_ddr5']->id,
            'quantity' => 12,
            'reserved_quantity' => 0,
        ]);

        Stock::create([
            'warehouse_id' => $warehouses['hw_lab']->id,
            'product_id' => $products['ssd_nvme']->id,
            'quantity' => 8,
            'reserved_quantity' => 0,
        ]);

        Stock::create([
            'warehouse_id' => $warehouses['hw_lab']->id,
            'product_id' => $products['psu_1200']->id,
            'quantity' => 4,
            'reserved_quantity' => 0,
        ]);

        Stock::create([
            'warehouse_id' => $warehouses['main']->id,
            'product_id' => $products['monitor']->id,
            'quantity' => 3,
            'reserved_quantity' => 0,
        ]);

        Stock::create([
            'warehouse_id' => $warehouses['main']->id,
            'product_id' => $products['keyboard']->id,
            'quantity' => 5,
            'reserved_quantity' => 0,
        ]);

        // 발주 3 - 스마트팩토리 서버 부품 (승인 대기)
        $po3 = PurchaseOrder::create([
            'po_number' => 'PO-' . $baseDate->format('Ymd') . '-0003',
            'supplier_id' => $suppliers['server_vendor']->id,
            'order_date' => $baseDate->copy(),
            'expected_date' => $baseDate->copy()->addDays(7),
            'subtotal' => 26000000,
            'tax_amount' => 2600000,
            'total_amount' => 28600000,
            'status' => '승인대기',
            'note' => '코리아매뉴팩처링 스마트팩토리 서버 부품',
            'shipping_address' => '경기도 화성시 동탄산업단지로 100',
            'created_by' => $users['hw_lead']->id,
        ]);

        PurchaseOrderItem::create([
            'purchase_order_id' => $po3->id,
            'product_id' => $products['srv_1u']->id,
            'description' => 'TW-R1000 1U 랙서버 (공장 서버)',
            'quantity' => 2,
            'unit' => '대',
            'unit_price' => 4500000,
            'tax_rate' => 10,
            'amount' => 9000000,
            'received_quantity' => 0,
        ]);

        PurchaseOrderItem::create([
            'purchase_order_id' => $po3->id,
            'product_id' => $products['switch_10g']->id,
            'description' => '10G 네트워크 스위치',
            'quantity' => 1,
            'unit' => '대',
            'unit_price' => 1800000,
            'tax_rate' => 10,
            'amount' => 1800000,
            'received_quantity' => 0,
        ]);

        PurchaseOrderItem::create([
            'purchase_order_id' => $po3->id,
            'product_id' => $products['ups']->id,
            'description' => '랙마운트 UPS 3kVA',
            'quantity' => 1,
            'unit' => '대',
            'unit_price' => 1500000,
            'tax_rate' => 10,
            'amount' => 1500000,
            'received_quantity' => 0,
        ]);

        // 발주 4 - 사무용품 (주변기기, 승인 완료)
        $po4 = PurchaseOrder::create([
            'po_number' => 'PO-' . $baseDate->copy()->subDays(5)->format('Ymd') . '-0004',
            'supplier_id' => $suppliers['office_vendor']->id,
            'order_date' => $baseDate->copy()->subDays(5),
            'expected_date' => $baseDate->copy()->addDays(2),
            'subtotal' => 3250000,
            'tax_amount' => 325000,
            'total_amount' => 3575000,
            'status' => '발주',
            'note' => '신규 입사자 장비 및 사무용품',
            'shipping_address' => '서울시 강남구 테헤란로 123, 테크웨이브빌딩',
            'created_by' => $users['account_member']->id,
            'approved_by' => $users['finance_head']->id,
            'approved_at' => $baseDate->copy()->subDays(4),
        ]);

        PurchaseOrderItem::create([
            'purchase_order_id' => $po4->id,
            'product_id' => $products['monitor']->id,
            'description' => 'Dell UltraSharp 27 4K (신규 입사자)',
            'quantity' => 3,
            'unit' => '대',
            'unit_price' => 650000,
            'tax_rate' => 10,
            'amount' => 1950000,
            'received_quantity' => 0,
        ]);

        PurchaseOrderItem::create([
            'purchase_order_id' => $po4->id,
            'product_id' => $products['keyboard']->id,
            'description' => '레오폴드 FC660M (신규 입사자)',
            'quantity' => 3,
            'unit' => '개',
            'unit_price' => 150000,
            'tax_rate' => 10,
            'amount' => 450000,
            'received_quantity' => 0,
        ]);
    }

    // ─────────────────────────────────────────────
    // 14. 청구서 및 결제 생성
    // ─────────────────────────────────────────────
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
            'created_by' => $users['account_member']->id,
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

        Payment::create([
            'payment_number' => 'PAY-' . $baseDate->copy()->subDays(5)->format('Ymd') . '-0001',
            'payable_type' => Invoice::class,
            'payable_id' => $inv1->id,
            'payment_date' => $baseDate->copy()->subDays(5),
            'amount' => 26400000,
            'method' => '계좌이체',
            'reference' => '넥스트게임즈 → 테크웨이브',
            'note' => '착수금 입금 완료',
            'recorded_by' => $users['account_member']->id,
        ]);

        // 청구서 2 - 결제 API 착수금 (미결제)
        $inv2 = Invoice::create([
            'invoice_number' => 'INV-' . $baseDate->copy()->subDays(8)->format('Ymd') . '-0002',
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
            'created_by' => $users['account_member']->id,
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

        // 청구서 3 - GPU 서버 납품 (결제 완료)
        $inv3 = Invoice::create([
            'invoice_number' => 'INV-' . $baseDate->copy()->subDays(10)->format('Ymd') . '-0003',
            'customer_id' => $customers['university']->id,
            'contract_id' => $contracts['university']->id,
            'project_id' => $projects['university']->id,
            'issue_date' => $baseDate->copy()->subDays(10),
            'due_date' => $baseDate->copy()->addDays(20),
            'subtotal' => 250000000,
            'tax_amount' => 25000000,
            'total_amount' => 275000000,
            'paid_amount' => 137500000,
            'status' => '부분결제',
            'note' => 'AI 연구 GPU 서버 클러스터 4대',
            'terms' => '납품 후 30일 이내',
            'created_by' => $users['finance_head']->id,
        ]);

        InvoiceItem::create([
            'invoice_id' => $inv3->id,
            'product_id' => $products['srv_gpu']->id,
            'description' => 'TW-G4000 GPU 서버 (A100 x 4 구성)',
            'quantity' => 4,
            'unit' => '대',
            'unit_price' => 62500000,
            'discount' => 0,
            'tax_rate' => 10,
            'amount' => 250000000,
        ]);

        Payment::create([
            'payment_number' => 'PAY-' . $baseDate->copy()->subDays(3)->format('Ymd') . '-0002',
            'payable_type' => Invoice::class,
            'payable_id' => $inv3->id,
            'payment_date' => $baseDate->copy()->subDays(3),
            'amount' => 137500000,
            'method' => '계좌이체',
            'reference' => 'KAIST → 테크웨이브 (1차 분할)',
            'note' => 'GPU 서버 1차 납품분 50% 입금',
            'recorded_by' => $users['finance_head']->id,
        ]);

        // 청구서 4 - 쇼핑몰 유지보수 (결제 완료)
        $inv4 = Invoice::create([
            'invoice_number' => 'INV-' . $baseDate->copy()->subDays(25)->format('Ymd') . '-0004',
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
            'created_by' => $users['account_member']->id,
        ]);

        InvoiceItem::create([
            'invoice_id' => $inv4->id,
            'product_id' => $products['monthly_maint']->id,
            'description' => '쇼핑몰 시스템 월간 유지보수 (1월)',
            'quantity' => 1,
            'unit' => '월',
            'unit_price' => 3000000,
            'discount' => 0,
            'tax_rate' => 10,
            'amount' => 3000000,
        ]);

        Payment::create([
            'payment_number' => 'PAY-' . $baseDate->copy()->subDays(12)->format('Ymd') . '-0003',
            'payable_type' => Invoice::class,
            'payable_id' => $inv4->id,
            'payment_date' => $baseDate->copy()->subDays(12),
            'amount' => 3300000,
            'method' => '계좌이체',
            'reference' => '쇼핑몰플러스 → 테크웨이브',
            'note' => '1월 유지보수 비용 입금',
            'recorded_by' => $users['account_member']->id,
        ]);

        // 청구서 5 - 2월 유지보수 (초안)
        Invoice::create([
            'invoice_number' => 'INV-' . $baseDate->format('Ymd') . '-0005',
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
            'created_by' => $users['account_member']->id,
        ]);
    }

    // ─────────────────────────────────────────────
    // 15. 비용 생성
    // ─────────────────────────────────────────────
    private function createExpenses(array $users, array $projects, array $suppliers): void
    {
        $baseDate = now();
        $categories = ExpenseCategory::all();

        $swCategory = $categories->where('name', '소프트웨어')->first() ?? $categories->first();
        $equipCategory = $categories->where('name', '장비/기기')->first() ?? $categories->first();
        $mealCategory = $categories->where('name', '식대')->first() ?? $categories->first();
        $travelCategory = $categories->where('name', '교통비')->first() ?? $categories->first();
        $trainCategory = $categories->where('name', '교육/훈련')->first() ?? $categories->first();
        $officeCategory = $categories->where('name', '사무용품')->first() ?? $categories->first();

        // 비용 1 - 클라우드 서비스 (SD)
        Expense::create([
            'expense_number' => 'EXP-' . $baseDate->copy()->subDays(15)->format('Ymd') . '-0001',
            'category_id' => $swCategory?->id,
            'employee_id' => Employee::where('user_id', $users['dev_lead']->id)->first()?->id,
            'supplier_id' => $suppliers['cloud_vendor']->id,
            'expense_date' => $baseDate->copy()->subDays(15),
            'title' => 'AWS 클라우드 서비스 1월분',
            'description' => 'EC2, RDS, S3 등 클라우드 인프라 비용',
            'amount' => 850000,
            'tax_amount' => 85000,
            'total_amount' => 935000,
            'status' => '승인',
            'approved_by' => $users['sd_head']->id,
            'approved_at' => $baseDate->copy()->subDays(14),
        ]);

        // 비용 2 - 고객 미팅 식대 (영업)
        Expense::create([
            'expense_number' => 'EXP-' . $baseDate->copy()->subDays(12)->format('Ymd') . '-0002',
            'category_id' => $mealCategory?->id,
            'employee_id' => Employee::where('user_id', $users['emb_lead']->id)->first()?->id,
            'project_id' => $projects['manufacturing']->id ?? null,
            'expense_date' => $baseDate->copy()->subDays(12),
            'title' => '코리아매뉴팩처링 현장 미팅 식대',
            'description' => '화성 공장 방문 점심 식사 (4명)',
            'amount' => 120000,
            'tax_amount' => 12000,
            'total_amount' => 132000,
            'status' => '승인',
            'approved_by' => $users['hd_head']->id,
            'approved_at' => $baseDate->copy()->subDays(11),
        ]);

        // 비용 3 - 출장 교통비 (HD)
        Expense::create([
            'expense_number' => 'EXP-' . $baseDate->copy()->subDays(10)->format('Ymd') . '-0003',
            'category_id' => $travelCategory?->id,
            'employee_id' => Employee::where('user_id', $users['hw_member']->id)->first()?->id,
            'project_id' => $projects['university']->id ?? null,
            'expense_date' => $baseDate->copy()->subDays(10),
            'title' => 'KAIST 서버실 출장 교통비',
            'description' => '대전 왕복 KTX + 택시비',
            'amount' => 130000,
            'tax_amount' => 0,
            'total_amount' => 130000,
            'status' => '승인',
            'approved_by' => $users['hw_lead']->id,
            'approved_at' => $baseDate->copy()->subDays(9),
        ]);

        // 비용 4 - 교육비 (AI팀)
        Expense::create([
            'expense_number' => 'EXP-' . $baseDate->copy()->subDays(7)->format('Ymd') . '-0004',
            'category_id' => $trainCategory?->id,
            'employee_id' => Employee::where('user_id', $users['ai_member']->id)->first()?->id,
            'expense_date' => $baseDate->copy()->subDays(7),
            'title' => 'LLM 파인튜닝 온라인 교육',
            'description' => 'Coursera LLM 파인튜닝 특화 과정 수강료',
            'amount' => 300000,
            'tax_amount' => 0,
            'total_amount' => 300000,
            'status' => '승인',
            'approved_by' => $users['ai_lead']->id,
            'approved_at' => $baseDate->copy()->subDays(6),
        ]);

        // 비용 5 - 사무용품 (경영지원)
        Expense::create([
            'expense_number' => 'EXP-' . $baseDate->copy()->subDays(5)->format('Ymd') . '-0005',
            'category_id' => $officeCategory?->id,
            'employee_id' => Employee::where('user_id', $users['account_member']->id)->first()?->id,
            'supplier_id' => $suppliers['office_vendor']->id,
            'expense_date' => $baseDate->copy()->subDays(5),
            'title' => '사무용품 일괄 구매',
            'description' => '화이트보드 마커, 포스트잇, 네임펜 등',
            'amount' => 85000,
            'tax_amount' => 8500,
            'total_amount' => 93500,
            'status' => '승인',
            'approved_by' => $users['finance_head']->id,
            'approved_at' => $baseDate->copy()->subDays(4),
        ]);

        // 비용 6 - 장비 구매 (승인 대기)
        Expense::create([
            'expense_number' => 'EXP-' . $baseDate->copy()->subDays(2)->format('Ymd') . '-0006',
            'category_id' => $equipCategory?->id,
            'employee_id' => Employee::where('user_id', $users['emb_member']->id)->first()?->id,
            'expense_date' => $baseDate->copy()->subDays(2),
            'title' => '오실로스코프 프로브 구매',
            'description' => 'Tektronix 오실로스코프 프로브 교체 (2개)',
            'amount' => 450000,
            'tax_amount' => 45000,
            'total_amount' => 495000,
            'status' => '대기',
        ]);

        // 비용 7 - 클라우드 비용 (승인 대기)
        Expense::create([
            'expense_number' => 'EXP-' . $baseDate->copy()->subDays(1)->format('Ymd') . '-0007',
            'category_id' => $swCategory?->id,
            'employee_id' => Employee::where('user_id', $users['ai_lead']->id)->first()?->id,
            'expense_date' => $baseDate->copy()->subDays(1),
            'title' => 'OpenAI API 사용료 1월분',
            'description' => 'GPT-4 API 호출 비용 (AI 연구용)',
            'amount' => 1200000,
            'tax_amount' => 0,
            'total_amount' => 1200000,
            'status' => '대기',
        ]);
    }
}
