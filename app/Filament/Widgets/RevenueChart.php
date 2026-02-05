<?php

namespace App\Filament\Widgets;

use App\Models\Expense;
use App\Models\Invoice;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class RevenueChart extends ChartWidget
{
    protected static ?string $heading = '월별 매출/비용 현황';

    protected static ?int $sort = 2;

    protected int | string | array $columnSpan = 'full';

    protected function getData(): array
    {
        $months = collect();
        $revenues = collect();
        $expenses = collect();

        // 최근 6개월 데이터
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $months->push($date->format('Y-m'));

            $monthStart = $date->copy()->startOfMonth();
            $monthEnd = $date->copy()->endOfMonth();

            // 매출 (결제 완료된 청구서)
            $revenue = Invoice::where('status', '결제완료')
                ->whereBetween('created_at', [$monthStart, $monthEnd])
                ->sum('total_amount');
            $revenues->push($revenue);

            // 비용 (승인된 비용)
            $expense = Expense::where('status', '승인')
                ->whereBetween('expense_date', [$monthStart, $monthEnd])
                ->sum('total_amount');
            $expenses->push($expense);
        }

        return [
            'datasets' => [
                [
                    'label' => '매출',
                    'data' => $revenues->toArray(),
                    'backgroundColor' => 'rgba(59, 130, 246, 0.5)',
                    'borderColor' => 'rgb(59, 130, 246)',
                ],
                [
                    'label' => '비용',
                    'data' => $expenses->toArray(),
                    'backgroundColor' => 'rgba(239, 68, 68, 0.5)',
                    'borderColor' => 'rgb(239, 68, 68)',
                ],
            ],
            'labels' => $months->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
