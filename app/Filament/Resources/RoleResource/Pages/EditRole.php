<?php

namespace App\Filament\Resources\RoleResource\Pages;

use App\Filament\Resources\RoleResource;
use Filament\Resources\Pages\Page;
use Filament\Resources\Pages\Concerns\InteractsWithRecord;
use Filament\Notifications\Notification;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class EditRole extends Page
{
    use InteractsWithRecord;

    protected static string $resource = RoleResource::class;

    protected static string $view = 'filament.resources.role-resource.pages.edit-role';

    public string $roleName = '';

    /**
     * 모듈별 권한 매트릭스 데이터
     * 구조: ['module_name' => ['action' => bool, ...], ...]
     */
    public array $permissions = [];

    /**
     * 시스템에 정의된 모듈과 액션 목록
     */
    protected static array $moduleDefinitions = [
        'user' => ['label' => '사용자', 'actions' => ['view', 'create', 'update', 'delete']],
        'department' => ['label' => '부서', 'actions' => ['view', 'create', 'update', 'delete']],
        'employee' => ['label' => '직원', 'actions' => ['view', 'create', 'update', 'delete']],
        'attendance' => ['label' => '근태', 'actions' => ['view', 'create', 'update', 'delete']],
        'leave' => ['label' => '휴가', 'actions' => ['view', 'create', 'update', 'delete', 'approve']],
        'customer' => ['label' => '고객', 'actions' => ['view', 'create', 'update', 'delete']],
        'contact' => ['label' => '연락처', 'actions' => ['view', 'create', 'update', 'delete']],
        'lead' => ['label' => '리드', 'actions' => ['view', 'create', 'update', 'delete', 'convert']],
        'opportunity' => ['label' => '영업 기회', 'actions' => ['view', 'create', 'update', 'delete']],
        'contract' => ['label' => '계약', 'actions' => ['view', 'create', 'update', 'delete', 'sign']],
        'invoice' => ['label' => '청구서', 'actions' => ['view', 'create', 'update', 'delete', 'approve']],
        'expense' => ['label' => '비용', 'actions' => ['view', 'create', 'update', 'delete', 'approve']],
        'payment' => ['label' => '결제', 'actions' => ['view', 'create', 'update', 'delete']],
        'project' => ['label' => '프로젝트', 'actions' => ['view', 'create', 'update', 'delete']],
        'task' => ['label' => '작업', 'actions' => ['view', 'create', 'update', 'delete']],
        'timesheet' => ['label' => '근무기록', 'actions' => ['view', 'create', 'update', 'delete', 'approve']],
        'supplier' => ['label' => '공급업체', 'actions' => ['view', 'create', 'update', 'delete']],
        'purchase_order' => ['label' => '구매주문', 'actions' => ['view', 'create', 'update', 'delete', 'approve']],
        'product' => ['label' => '상품', 'actions' => ['view', 'create', 'update', 'delete']],
        'warehouse' => ['label' => '창고', 'actions' => ['view', 'create', 'update', 'delete']],
        'stock' => ['label' => '재고', 'actions' => ['view', 'create', 'update', 'delete', 'adjust']],
        'account' => ['label' => '계정과목', 'actions' => ['view', 'create', 'update', 'delete']],
        'report' => ['label' => '리포트', 'actions' => ['view', 'export']],
        'setting' => ['label' => '설정', 'actions' => ['view', 'update']],
    ];

    /**
     * 액션 라벨 매핑
     */
    protected static array $actionLabels = [
        'view' => '보기',
        'create' => '생성',
        'update' => '수정',
        'delete' => '삭제',
        'approve' => '승인',
        'sign' => '서명',
        'convert' => '전환',
        'export' => '내보내기',
        'adjust' => '조정',
    ];

    public function mount(int|string $record): void
    {
        $this->record = $this->resolveRecord($record);
        $this->roleName = $this->record->name;

        // Super Admin은 편집 불가
        if ($this->record->name === 'Super Admin') {
            $this->redirect(RoleResource::getUrl('index'));
            return;
        }

        $this->loadPermissions();
    }

    protected function loadPermissions(): void
    {
        $rolePermissions = $this->record->permissions->pluck('name')->toArray();

        foreach (static::$moduleDefinitions as $module => $config) {
            foreach ($config['actions'] as $action) {
                $permName = "{$module}.{$action}";
                $this->permissions[$module][$action] = in_array($permName, $rolePermissions);
            }
        }
    }

    /**
     * 권한 저장
     */
    public function save(): void
    {
        $selectedPermissions = [];

        foreach ($this->permissions as $module => $actions) {
            foreach ($actions as $action => $enabled) {
                if ($enabled) {
                    $selectedPermissions[] = "{$module}.{$action}";
                }
            }
        }

        // DB에 존재하는 권한만 필터
        $validPermissions = Permission::whereIn('name', $selectedPermissions)
            ->pluck('name')
            ->toArray();

        $this->record->syncPermissions($validPermissions);

        // 권한 캐시 초기화
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        Notification::make()
            ->title('권한이 저장되었습니다.')
            ->success()
            ->send();
    }

    /**
     * 모듈 전체 선택/해제 토글
     */
    public function toggleModule(string $module): void
    {
        if (!isset(static::$moduleDefinitions[$module])) return;

        $actions = static::$moduleDefinitions[$module]['actions'];
        $allChecked = collect($actions)->every(fn($a) => $this->permissions[$module][$a] ?? false);

        foreach ($actions as $action) {
            $this->permissions[$module][$action] = !$allChecked;
        }
    }

    /**
     * 전체 선택
     */
    public function selectAll(): void
    {
        foreach (static::$moduleDefinitions as $module => $config) {
            foreach ($config['actions'] as $action) {
                $this->permissions[$module][$action] = true;
            }
        }
    }

    /**
     * 전체 해제
     */
    public function deselectAll(): void
    {
        foreach (static::$moduleDefinitions as $module => $config) {
            foreach ($config['actions'] as $action) {
                $this->permissions[$module][$action] = false;
            }
        }
    }

    public function getTitle(): string
    {
        return "역할 편집: {$this->roleName}";
    }

    /**
     * 뷰에서 사용할 모듈 정의 반환
     */
    public function getModuleDefinitionsProperty(): array
    {
        return static::$moduleDefinitions;
    }

    /**
     * 뷰에서 사용할 액션 라벨 반환
     */
    public function getActionLabelsProperty(): array
    {
        return static::$actionLabels;
    }

    /**
     * 모든 유니크 액션 목록 반환 (테이블 헤더용)
     */
    public function getAllActionsProperty(): array
    {
        $actions = [];
        foreach (static::$moduleDefinitions as $config) {
            foreach ($config['actions'] as $action) {
                $actions[$action] = true;
            }
        }

        return array_keys($actions);
    }

    protected function getHeaderActions(): array
    {
        return [];
    }

    public function getBreadcrumbs(): array
    {
        return [
            RoleResource::getUrl() => '역할 관리',
            '#' => "역할 편집: {$this->roleName}",
        ];
    }
}
