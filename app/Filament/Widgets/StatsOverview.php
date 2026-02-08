<?php

namespace App\Filament\Widgets;

use App\Enums\EmployeeStatus;
use App\Enums\ExpenseStatus;
use App\Enums\InvoiceStatus;
use App\Enums\ProjectStatus;
use App\Enums\TaskStatus;
use App\Models\Customer;
use App\Models\Employee;
use App\Models\Expense;
use App\Models\Invoice;
use App\Models\Leave;
use App\Models\Project;
use App\Models\Task;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $user = auth()->user();

        // Manager 이상: 전체 경영 통계
        if ($user->hasAnyRole(['Super Admin', 'Admin', 'Manager'])) {
            return $this->getManagerStats();
        }

        // Accountant: 재무 통계
        if ($user->hasRole('Accountant')) {
            return $this->getAccountantStats();
        }

        // HR Manager: 인사 통계
        if ($user->hasRole('HR Manager')) {
            return $this->getHrStats();
        }

        // Employee: 본인 관련 통계
        return $this->getEmployeeStats();
    }

    protected function getManagerStats(): array
    {
        $thisMonth = now()->startOfMonth();
        $lastMonth = now()->subMonth()->startOfMonth();

        $thisMonthRevenue = Invoice::where('status', InvoiceStatus::Paid->value)
            ->where('created_at', '>=', $thisMonth)
            ->sum('total_amount');

        $lastMonthRevenue = Invoice::where('status', InvoiceStatus::Paid->value)
            ->whereBetween('created_at', [$lastMonth, $thisMonth])
            ->sum('total_amount');

        $revenueChange = $lastMonthRevenue > 0
            ? round((($thisMonthRevenue - $lastMonthRevenue) / $lastMonthRevenue) * 100, 1)
            : 0;

        $pendingInvoices = Invoice::whereIn('status', [InvoiceStatus::Issued->value, InvoiceStatus::PartiallyPaid->value, InvoiceStatus::Overdue->value])->sum('total_amount') -
            Invoice::whereIn('status', [InvoiceStatus::Issued->value, InvoiceStatus::PartiallyPaid->value, InvoiceStatus::Overdue->value])->sum('paid_amount');

        $activeProjects = Project::where('status', ProjectStatus::InProgress->value)->count();
        $totalCustomers = Customer::where('status', 'active')->count();
        $activeEmployees = Employee::where('status', EmployeeStatus::Active->value)->count();

        return [
            Stat::make(__('common.stats.monthly_revenue'), '₩' . number_format($thisMonthRevenue))
                ->description($revenueChange >= 0
                    ? __('common.stats.increase', ['value' => $revenueChange])
                    : __('common.stats.decrease', ['value' => $revenueChange]))
                ->descriptionIcon($revenueChange >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($revenueChange >= 0 ? 'success' : 'danger'),

            Stat::make(__('common.stats.pending_amount'), '₩' . number_format($pendingInvoices))
                ->description(__('common.stats.payment_pending'))
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning'),

            Stat::make(__('common.stats.active_projects'), __('common.stats.projects_count', ['count' => $activeProjects]))
                ->description(__('common.stats.active_projects_desc'))
                ->descriptionIcon('heroicon-m-clipboard-document-list')
                ->color('info'),

            Stat::make(__('common.stats.customers_employees'), __('common.stats.customers_employees_value', ['customers' => $totalCustomers, 'employees' => $activeEmployees]))
                ->description(__('common.stats.active_customers_employees'))
                ->descriptionIcon('heroicon-m-users')
                ->color('success'),
        ];
    }

    protected function getAccountantStats(): array
    {
        $thisMonth = now()->startOfMonth();

        $thisMonthRevenue = Invoice::where('status', InvoiceStatus::Paid->value)
            ->where('created_at', '>=', $thisMonth)
            ->sum('total_amount');

        $pendingInvoices = Invoice::whereIn('status', [InvoiceStatus::Issued->value, InvoiceStatus::PartiallyPaid->value, InvoiceStatus::Overdue->value])->sum('total_amount') -
            Invoice::whereIn('status', [InvoiceStatus::Issued->value, InvoiceStatus::PartiallyPaid->value, InvoiceStatus::Overdue->value])->sum('paid_amount');

        $thisMonthExpense = Expense::where('status', ExpenseStatus::Approved->value)
            ->where('expense_date', '>=', $thisMonth)
            ->sum('total_amount');

        $pendingExpenses = Expense::where('status', ExpenseStatus::Pending->value)->count();

        return [
            Stat::make(__('common.stats.monthly_revenue'), '₩' . number_format($thisMonthRevenue))
                ->description(__('common.stats.paid_basis'))
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('success'),

            Stat::make(__('common.stats.pending_amount'), '₩' . number_format($pendingInvoices))
                ->description(__('common.stats.payment_pending'))
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning'),

            Stat::make(__('common.stats.monthly_expense'), '₩' . number_format($thisMonthExpense))
                ->description(__('common.stats.approved_expense'))
                ->descriptionIcon('heroicon-m-receipt-percent')
                ->color('danger'),

            Stat::make(__('common.stats.pending_approval_expense'), __('common.stats.count_suffix', ['count' => $pendingExpenses]))
                ->description(__('common.stats.needs_review'))
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color('warning'),
        ];
    }

    protected function getHrStats(): array
    {
        $activeEmployees = Employee::where('status', EmployeeStatus::Active->value)->count();

        $pendingLeaves = Leave::where('status', 'pending')->count();

        $todayAttendance = \App\Models\Attendance::whereDate('date', today())->count();

        $newEmployeesThisMonth = Employee::where('hire_date', '>=', now()->startOfMonth())->count();

        return [
            Stat::make(__('common.stats.active_employees'), __('common.stats.person_count', ['count' => $activeEmployees]))
                ->description(__('common.stats.currently_active'))
                ->descriptionIcon('heroicon-m-users')
                ->color('success'),

            Stat::make(__('common.stats.today_attendance'), __('common.stats.person_count', ['count' => $todayAttendance]))
                ->description(__('common.stats.today_attendance_desc'))
                ->descriptionIcon('heroicon-m-clock')
                ->color('info'),

            Stat::make(__('common.stats.pending_leave'), __('common.stats.count_suffix', ['count' => $pendingLeaves]))
                ->description(__('common.stats.needs_review'))
                ->descriptionIcon('heroicon-m-calendar')
                ->color('warning'),

            Stat::make(__('common.stats.new_hires_month'), __('common.stats.person_count', ['count' => $newEmployeesThisMonth]))
                ->description(__('common.stats.year_month_format', ['year' => now()->format('Y'), 'month' => now()->format('m')]))
                ->descriptionIcon('heroicon-m-user-plus')
                ->color('info'),
        ];
    }

    protected function getEmployeeStats(): array
    {
        $user = auth()->user();
        $employee = $user->employee;

        $myTasks = Task::where('assigned_to', $user->id)
            ->where('status', '!=', TaskStatus::Completed->value)
            ->count();

        $myProjects = Project::where('status', ProjectStatus::InProgress->value)
            ->where(function ($q) use ($user) {
                $q->where('manager_id', $user->id)
                  ->orWhereHas('members', fn ($mq) => $mq->where('users.id', $user->id));
            })
            ->count();

        $remainingLeave = $employee?->remaining_leave_days ?? 0;

        $pendingExpenses = $employee
            ? Expense::where('employee_id', $employee->id)->where('status', ExpenseStatus::Pending->value)->count()
            : 0;

        return [
            Stat::make(__('common.stats.my_tasks'), __('common.stats.count_suffix', ['count' => $myTasks]))
                ->description(__('common.stats.in_progress_tasks'))
                ->descriptionIcon('heroicon-m-clipboard-document-check')
                ->color('info'),

            Stat::make(__('common.stats.participating_projects'), __('common.stats.projects_count', ['count' => $myProjects]))
                ->description(__('common.stats.in_progress'))
                ->descriptionIcon('heroicon-m-briefcase')
                ->color('success'),

            Stat::make(__('common.stats.remaining_leave'), __('common.stats.day_count', ['count' => $remainingLeave]))
                ->description(__('common.stats.year_basis', ['year' => now()->format('Y')]))
                ->descriptionIcon('heroicon-m-calendar-days')
                ->color('warning'),

            Stat::make(__('common.stats.pending_expenses'), __('common.stats.count_suffix', ['count' => $pendingExpenses]))
                ->description(__('common.stats.submitted_expenses'))
                ->descriptionIcon('heroicon-m-receipt-percent')
                ->color($pendingExpenses > 0 ? 'warning' : 'success'),
        ];
    }
}
