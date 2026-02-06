<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 권한 캐시 초기화
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // 모듈별 권한 생성
        $modules = [
            'user' => ['view', 'create', 'update', 'delete'],
            'department' => ['view', 'create', 'update', 'delete'],
            'employee' => ['view', 'create', 'update', 'delete'],
            'attendance' => ['view', 'create', 'update', 'delete'],
            'leave' => ['view', 'create', 'update', 'delete', 'approve'],
            'customer' => ['view', 'create', 'update', 'delete'],
            'contact' => ['view', 'create', 'update', 'delete'],
            'lead' => ['view', 'create', 'update', 'delete', 'convert'],
            'opportunity' => ['view', 'create', 'update', 'delete'],
            'contract' => ['view', 'create', 'update', 'delete', 'sign'],
            'invoice' => ['view', 'create', 'update', 'delete', 'approve'],
            'expense' => ['view', 'create', 'update', 'delete', 'approve'],
            'payment' => ['view', 'create', 'update', 'delete'],
            'project' => ['view', 'create', 'update', 'delete'],
            'task' => ['view', 'create', 'update', 'delete'],
            'timesheet' => ['view', 'create', 'update', 'delete', 'approve'],
            'supplier' => ['view', 'create', 'update', 'delete'],
            'purchase_order' => ['view', 'create', 'update', 'delete', 'approve'],
            'product' => ['view', 'create', 'update', 'delete'],
            'stock' => ['view', 'create', 'update', 'delete', 'adjust'],
            'warehouse' => ['view', 'create', 'update', 'delete'],
            'account' => ['view', 'create', 'update', 'delete'],
            'report' => ['view', 'export'],
            'setting' => ['view', 'update'],
        ];

        foreach ($modules as $module => $actions) {
            foreach ($actions as $action) {
                Permission::firstOrCreate(['name' => "{$module}.{$action}"]);
            }
        }

        // 역할 생성
        $superAdmin = Role::firstOrCreate(['name' => 'Super Admin']);
        $admin = Role::firstOrCreate(['name' => 'Admin']);
        $manager = Role::firstOrCreate(['name' => 'Manager']);
        $accountant = Role::firstOrCreate(['name' => 'Accountant']);
        $hrManager = Role::firstOrCreate(['name' => 'HR Manager']);
        $employee = Role::firstOrCreate(['name' => 'Employee']);

        // Super Admin은 Gate::before에서 처리되므로 별도 권한 부여 불필요

        // Admin - 설정 제외 모든 권한
        $adminPermissions = Permission::where('name', 'not like', 'setting.%')->get();
        $admin->syncPermissions($adminPermissions);

        // Manager - 승인 권한 포함한 일반 관리
        $managerPermissions = Permission::whereIn('name', [
            'employee.view', 'employee.update',
            'attendance.view', 'attendance.create', 'attendance.update',
            'leave.view', 'leave.create', 'leave.update', 'leave.approve',
            'customer.view', 'customer.create', 'customer.update',
            'contact.view', 'contact.create', 'contact.update',
            'lead.view', 'lead.create', 'lead.update', 'lead.convert',
            'opportunity.view', 'opportunity.create', 'opportunity.update',
            'contract.view',
            'invoice.view',
            'expense.view', 'expense.create', 'expense.update', 'expense.approve',
            'project.view', 'project.create', 'project.update',
            'task.view', 'task.create', 'task.update', 'task.delete',
            'timesheet.view', 'timesheet.create', 'timesheet.update', 'timesheet.approve',
            'supplier.view',
            'product.view',
            'warehouse.view',
            'stock.view',
            'report.view', 'report.export',
        ])->get();
        $manager->syncPermissions($managerPermissions);

        // Accountant - 재무/회계 관련 권한
        $accountantPermissions = Permission::whereIn('name', [
            'customer.view',
            'contract.view',
            'invoice.view', 'invoice.create', 'invoice.update', 'invoice.delete', 'invoice.approve',
            'expense.view', 'expense.create', 'expense.update', 'expense.delete', 'expense.approve',
            'payment.view', 'payment.create', 'payment.update', 'payment.delete',
            'supplier.view', 'supplier.create', 'supplier.update',
            'purchase_order.view', 'purchase_order.create', 'purchase_order.update', 'purchase_order.approve',
            'account.view', 'account.create', 'account.update', 'account.delete',
            'warehouse.view',
            'stock.view',
            'report.view', 'report.export',
        ])->get();
        $accountant->syncPermissions($accountantPermissions);

        // HR Manager - 인사 관련 권한
        $hrPermissions = Permission::whereIn('name', [
            'user.view', 'user.create', 'user.update',
            'department.view', 'department.create', 'department.update',
            'employee.view', 'employee.create', 'employee.update', 'employee.delete',
            'attendance.view', 'attendance.create', 'attendance.update', 'attendance.delete',
            'leave.view', 'leave.create', 'leave.update', 'leave.delete', 'leave.approve',
            'report.view', 'report.export',
        ])->get();
        $hrManager->syncPermissions($hrPermissions);

        // Employee - 기본 조회 및 본인 관련 권한
        $employeePermissions = Permission::whereIn('name', [
            'attendance.view', 'attendance.create',
            'leave.view', 'leave.create',
            'expense.view', 'expense.create',
            'task.view', 'task.update',
            'timesheet.view', 'timesheet.create', 'timesheet.update',
            'project.view',
        ])->get();
        $employee->syncPermissions($employeePermissions);
    }
}
