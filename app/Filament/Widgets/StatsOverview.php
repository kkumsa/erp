<?php

namespace App\Filament\Widgets;

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

        $thisMonthRevenue = Invoice::where('status', '결제완료')
            ->where('created_at', '>=', $thisMonth)
            ->sum('total_amount');

        $lastMonthRevenue = Invoice::where('status', '결제완료')
            ->whereBetween('created_at', [$lastMonth, $thisMonth])
            ->sum('total_amount');

        $revenueChange = $lastMonthRevenue > 0
            ? round((($thisMonthRevenue - $lastMonthRevenue) / $lastMonthRevenue) * 100, 1)
            : 0;

        $pendingInvoices = Invoice::whereIn('status', ['발행', '부분결제', '연체'])->sum('total_amount') -
            Invoice::whereIn('status', ['발행', '부분결제', '연체'])->sum('paid_amount');

        $activeProjects = Project::where('status', '진행중')->count();
        $totalCustomers = Customer::where('status', '활성')->count();
        $activeEmployees = Employee::where('status', '재직')->count();

        return [
            Stat::make('이번 달 매출', '₩' . number_format($thisMonthRevenue))
                ->description($revenueChange >= 0 ? "{$revenueChange}% 증가" : "{$revenueChange}% 감소")
                ->descriptionIcon($revenueChange >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($revenueChange >= 0 ? 'success' : 'danger'),

            Stat::make('미결제 금액', '₩' . number_format($pendingInvoices))
                ->description('결제 대기 중')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning'),

            Stat::make('진행 중 프로젝트', $activeProjects . '개')
                ->description('활성 프로젝트')
                ->descriptionIcon('heroicon-m-clipboard-document-list')
                ->color('info'),

            Stat::make('고객 / 직원', "{$totalCustomers}개사 / {$activeEmployees}명")
                ->description('활성 고객 / 재직 직원')
                ->descriptionIcon('heroicon-m-users')
                ->color('success'),
        ];
    }

    protected function getAccountantStats(): array
    {
        $thisMonth = now()->startOfMonth();

        $thisMonthRevenue = Invoice::where('status', '결제완료')
            ->where('created_at', '>=', $thisMonth)
            ->sum('total_amount');

        $pendingInvoices = Invoice::whereIn('status', ['발행', '부분결제', '연체'])->sum('total_amount') -
            Invoice::whereIn('status', ['발행', '부분결제', '연체'])->sum('paid_amount');

        $thisMonthExpense = Expense::where('status', '승인')
            ->where('expense_date', '>=', $thisMonth)
            ->sum('total_amount');

        $pendingExpenses = Expense::where('status', '제출')->count();

        return [
            Stat::make('이번 달 매출', '₩' . number_format($thisMonthRevenue))
                ->description('결제완료 기준')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('success'),

            Stat::make('미결제 금액', '₩' . number_format($pendingInvoices))
                ->description('결제 대기 중')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning'),

            Stat::make('이번 달 비용', '₩' . number_format($thisMonthExpense))
                ->description('승인된 비용')
                ->descriptionIcon('heroicon-m-receipt-percent')
                ->color('danger'),

            Stat::make('승인 대기 비용', $pendingExpenses . '건')
                ->description('검토 필요')
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color('warning'),
        ];
    }

    protected function getHrStats(): array
    {
        $activeEmployees = Employee::where('status', '재직')->count();

        $pendingLeaves = Leave::where('status', '대기')->count();

        $todayAttendance = \App\Models\Attendance::whereDate('date', today())->count();

        $newEmployeesThisMonth = Employee::where('hire_date', '>=', now()->startOfMonth())->count();

        return [
            Stat::make('재직 직원', $activeEmployees . '명')
                ->description('현재 재직 중')
                ->descriptionIcon('heroicon-m-users')
                ->color('success'),

            Stat::make('오늘 출근', $todayAttendance . '명')
                ->description('금일 출근 현황')
                ->descriptionIcon('heroicon-m-clock')
                ->color('info'),

            Stat::make('휴가 승인 대기', $pendingLeaves . '건')
                ->description('검토 필요')
                ->descriptionIcon('heroicon-m-calendar')
                ->color('warning'),

            Stat::make('이번 달 신규 입사', $newEmployeesThisMonth . '명')
                ->description(now()->format('Y년 m월'))
                ->descriptionIcon('heroicon-m-user-plus')
                ->color('info'),
        ];
    }

    protected function getEmployeeStats(): array
    {
        $user = auth()->user();
        $employee = $user->employee;

        $myTasks = Task::where('assigned_to', $user->id)
            ->where('status', '!=', '완료')
            ->count();

        $myProjects = Project::where('status', '진행중')
            ->where(function ($q) use ($user) {
                $q->where('manager_id', $user->id)
                  ->orWhereHas('members', fn ($mq) => $mq->where('users.id', $user->id));
            })
            ->count();

        $remainingLeave = $employee?->remaining_leave_days ?? 0;

        $pendingExpenses = $employee
            ? Expense::where('employee_id', $employee->id)->where('status', '제출')->count()
            : 0;

        return [
            Stat::make('내 작업', $myTasks . '건')
                ->description('진행 중인 작업')
                ->descriptionIcon('heroicon-m-clipboard-document-check')
                ->color('info'),

            Stat::make('참여 프로젝트', $myProjects . '개')
                ->description('진행 중')
                ->descriptionIcon('heroicon-m-briefcase')
                ->color('success'),

            Stat::make('남은 연차', $remainingLeave . '일')
                ->description(now()->format('Y') . '년 기준')
                ->descriptionIcon('heroicon-m-calendar-days')
                ->color('warning'),

            Stat::make('비용 처리 대기', $pendingExpenses . '건')
                ->description('제출한 비용')
                ->descriptionIcon('heroicon-m-receipt-percent')
                ->color($pendingExpenses > 0 ? 'warning' : 'success'),
        ];
    }
}
