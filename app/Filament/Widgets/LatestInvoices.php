<?php

namespace App\Filament\Widgets;

use App\Enums\InvoiceStatus;
use App\Filament\Resources\InvoiceResource;
use App\Models\Invoice;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class LatestInvoices extends BaseWidget
{
    protected static ?int $sort = 3;

    protected int | string | array $columnSpan = 'full';

    public function getTableHeading(): string|\Illuminate\Contracts\Support\Htmlable|null
    {
        return __('common.widgets.latest_invoices');
    }

    public static function canView(): bool
    {
        $user = auth()->user();

        return $user && $user->hasAnyRole(['Super Admin', 'Admin', 'Manager', 'Accountant']);
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Invoice::query()
                    ->latest()
                    ->limit(5)
            )
            ->columns([
                Tables\Columns\TextColumn::make('invoice_number')
                    ->label(__('fields.invoice_number')),

                Tables\Columns\TextColumn::make('customer.company_name')
                    ->label(__('fields.customer')),

                Tables\Columns\TextColumn::make('issue_date')
                    ->label(__('fields.issue_date'))
                    ->date('Y.m.d'),

                Tables\Columns\TextColumn::make('due_date')
                    ->label(__('fields.due_date'))
                    ->date('Y.m.d')
                    ->color(fn ($record) => $record->is_overdue ? 'danger' : null),

                Tables\Columns\TextColumn::make('total_amount')
                    ->label(__('fields.amount'))
                    ->money('KRW'),

                Tables\Columns\TextColumn::make('status')
                    ->label(__('fields.status'))
                    ->badge()
                    ->formatStateUsing(fn ($state) => $state instanceof InvoiceStatus ? $state->getLabel() : (InvoiceStatus::tryFrom($state)?->getLabel() ?? $state))
                    ->color(fn ($state) => $state instanceof InvoiceStatus ? $state->color() : (InvoiceStatus::tryFrom($state)?->color() ?? 'gray')),
            ])
            ->actions([
                Tables\Actions\Action::make('view')
                    ->label(__('common.buttons.view'))
                    ->url(fn (Invoice $record): string => InvoiceResource::getUrl('view', ['record' => $record])),
            ])
            ->paginated(false);
    }
}
