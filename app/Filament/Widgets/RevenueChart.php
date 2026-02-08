<?php

namespace App\Filament\Widgets;

use App\Enums\ExpenseStatus;
use App\Enums\InvoiceStatus;
use App\Models\Expense;
use App\Models\Invoice;
use Filament\Widgets\ChartWidget;

class RevenueChart extends ChartWidget
{
    protected static ?int $sort = 2;

    protected int | string | array $columnSpan = 'full';

    public function getHeading(): string|\Illuminate\Contracts\Support\Htmlable|null
    {
        return __('common.widgets.monthly_revenue_expense');
    }

    public static function canView(): bool
    {
        $user = auth()->user();

        return $user && $user->hasAnyRole(['Super Admin', 'Admin', 'Manager', 'Accountant']);
    }

    protected function getData(): array
    {
        $months = collect();
        $revenues = collect();
        $expenses = collect();

        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $months->push($date->format('Y-m'));

            $monthStart = $date->copy()->startOfMonth();
            $monthEnd = $date->copy()->endOfMonth();

            $revenue = Invoice::where('status', InvoiceStatus::Paid->value)
                ->whereBetween('created_at', [$monthStart, $monthEnd])
                ->sum('total_amount');
            $revenues->push($revenue);

            $expense = Expense::where('status', ExpenseStatus::Approved->value)
                ->whereBetween('expense_date', [$monthStart, $monthEnd])
                ->sum('total_amount');
            $expenses->push($expense);
        }

        return [
            'datasets' => [
                [
                    'label' => __('common.widgets.revenue'),
                    'data' => $revenues->toArray(),
                    'backgroundColor' => 'rgba(59, 130, 246, 0.5)',
                    'borderColor' => 'rgb(59, 130, 246)',
                ],
                [
                    'label' => __('common.widgets.expense'),
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
