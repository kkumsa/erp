<?php

namespace App\Filament\Traits;

use Illuminate\Database\Eloquent\Model;

/**
 * Filament Resource에 Spatie Permission 기반 권한 강제를 자동 적용하는 Trait.
 *
 * 사용법: Resource 클래스에서
 *   use HasResourcePermissions;
 *   protected static ?string $permissionPrefix = 'customer';
 *
 * 이렇게 하면 customer.view / customer.create / customer.update / customer.delete 권한을
 * 자동으로 canViewAny / canCreate / canUpdate / canDelete에 매핑합니다.
 */
trait HasResourcePermissions
{
    /**
     * 퍼미션 프리픽스. 각 리소스에서 오버라이드.
     * 예: 'customer', 'project', 'invoice'
     */
    // protected static ?string $permissionPrefix = null;

    /**
     * 사이드바 메뉴 노출 여부
     */
    public static function shouldRegisterNavigation(): bool
    {
        if (!static::getPermissionPrefix()) {
            return parent::shouldRegisterNavigation();
        }

        $user = auth()->user();
        if (!$user) return false;

        return $user->can(static::getPermissionPrefix() . '.view');
    }

    /**
     * 리스트(목록) 접근 권한
     */
    public static function canViewAny(): bool
    {
        if (!static::getPermissionPrefix()) {
            return true;
        }

        $user = auth()->user();
        if (!$user) return false;

        return $user->can(static::getPermissionPrefix() . '.view');
    }

    /**
     * 상세 보기 권한
     */
    public static function canView(Model $record): bool
    {
        if (!static::getPermissionPrefix()) {
            return true;
        }

        $user = auth()->user();
        if (!$user) return false;

        return $user->can(static::getPermissionPrefix() . '.view');
    }

    /**
     * 생성 권한
     */
    public static function canCreate(): bool
    {
        if (!static::getPermissionPrefix()) {
            return true;
        }

        $user = auth()->user();
        if (!$user) return false;

        return $user->can(static::getPermissionPrefix() . '.create');
    }

    /**
     * 수정 권한
     */
    public static function canEdit(Model $record): bool
    {
        if (!static::getPermissionPrefix()) {
            return true;
        }

        $user = auth()->user();
        if (!$user) return false;

        return $user->can(static::getPermissionPrefix() . '.update');
    }

    /**
     * 삭제 권한
     */
    public static function canDelete(Model $record): bool
    {
        if (!static::getPermissionPrefix()) {
            return true;
        }

        $user = auth()->user();
        if (!$user) return false;

        return $user->can(static::getPermissionPrefix() . '.delete');
    }

    /**
     * 일괄 삭제 권한
     */
    public static function canDeleteAny(): bool
    {
        if (!static::getPermissionPrefix()) {
            return true;
        }

        $user = auth()->user();
        if (!$user) return false;

        return $user->can(static::getPermissionPrefix() . '.delete');
    }

    /**
     * 퍼미션 프리픽스 반환
     */
    protected static function getPermissionPrefix(): ?string
    {
        return static::$permissionPrefix ?? null;
    }
}
