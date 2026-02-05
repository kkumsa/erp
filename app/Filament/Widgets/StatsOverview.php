<?php

namespace App\Filament\Widgets;

use App\Models\Customer;
use App\Models\Employee;
use App\Models\Invoice;
use App\Models\Project;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $thisMonth = now()->startOfMonth();
        $lastMonth = now()->subMonth()->startOfMonth();

        // 이번 달 매출
        $thisMonthRevenue = Invoice::where('status', '결제완료')
            ->where('created_at', '>=', $thisMonth)
            ->sum('total_amount');

        // 지난 달 매출
        $lastMonthRevenue = Invoice::where('status', '결제완료')
            ->whereBetween('created_at', [$lastMonth, $thisMonth])
            ->sum('total_amount');

        // 매출 증감률
        $revenueChange = $lastMonthRevenue > 0
            ? round((($thisMonthRevenue - $lastMonthRevenue) / $lastMonthRevenue) * 100, 1)
            : 0;

        // 미결제 청구서
        $pendingInvoices = Invoice::whereIn('status', ['발행', '부분결제', '연체'])->sum('total_amount') -
            Invoice::whereIn('status', ['발행', '부분결제', '연체'])->sum('paid_amount');

        // 진행 중인 프로젝트
        $activeProjects = Project::where('status', '진행중')->count();

        // 총 고객 수
        $totalCustomers = Customer::where('status', '활성')->count();

        // 재직 중인 직원 수
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
}
