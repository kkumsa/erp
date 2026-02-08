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
        'user' => ['actions' => ['view', 'create', 'update', 'delete']],
        'department' => ['actions' => ['view', 'create', 'update', 'delete']],
        'employee' => ['actions' => ['view', 'create', 'update', 'delete']],
        'attendance' => ['actions' => ['view', 'create', 'update', 'delete']],
        'leave' => ['actions' => ['view', 'create', 'update', 'delete', 'approve']],
        'customer' => ['actions' => ['view', 'create', 'update', 'delete']],
        'contact' => ['actions' => ['view', 'create', 'update', 'delete']],
        'lead' => ['actions' => ['view', 'create', 'update', 'delete', 'convert']],
        'opportunity' => ['actions' => ['view', 'create', 'update', 'delete']],
        'contract' => ['actions' => ['view', 'create', 'update', 'delete', 'sign']],
        'invoice' => ['actions' => ['view', 'create', 'update', 'delete', 'approve']],
        'expense' => ['actions' => ['view', 'create', 'update', 'delete', 'approve']],
        'payment' => ['actions' => ['view', 'create', 'update', 'delete']],
        'project' => ['actions' => ['view', 'create', 'update', 'delete']],
        'task' => ['actions' => ['view', 'create', 'update', 'delete']],
        'timesheet' => ['actions' => ['view', 'create', 'update', 'delete', 'approve']],
        'supplier' => ['actions' => ['view', 'create', 'update', 'delete']],
        'purchase_order' => ['actions' => ['view', 'create', 'update', 'delete', 'approve']],
        'product' => ['actions' => ['view', 'create', 'update', 'delete']],
        'warehouse' => ['actions' => ['view', 'create', 'update', 'delete']],
        'stock' => ['actions' => ['view', 'create', 'update', 'delete', 'adjust']],
        'account' => ['actions' => ['view', 'create', 'update', 'delete']],
        'report' => ['actions' => ['view', 'export']],
        'setting' => ['actions' => ['view', 'update']],
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
            ->title(__('common.permissions.saved'))
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
        return __('common.permissions.edit_title', ['name' => $this->roleName]);
    }

    /**
     * 뷰에서 사용할 모듈 정의 반환 (라벨을 런타임에 번역)
     */
    public function getModuleDefinitionsProperty(): array
    {
        return collect(static::$moduleDefinitions)->map(function ($config, $module) {
            return array_merge($config, [
                'label' => __("models.{$module}"),
            ]);
        })->toArray();
    }

    /**
     * 뷰에서 사용할 액션 라벨 반환 (런타임에 번역)
     */
    public function getActionLabelsProperty(): array
    {
        $labels = [];
        foreach (['view', 'create', 'update', 'delete', 'approve', 'sign', 'convert', 'export', 'adjust'] as $action) {
            $labels[$action] = __("common.permissions.actions.{$action}");
        }
        return $labels;
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
            RoleResource::getUrl() => __('navigation.labels.role'),
            '#' => __('common.permissions.edit_title', ['name' => $this->roleName]),
        ];
    }
}
