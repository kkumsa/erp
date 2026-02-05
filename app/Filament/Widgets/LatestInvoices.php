<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\InvoiceResource;
use App\Models\Invoice;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class LatestInvoices extends BaseWidget
{
    protected static ?string $heading = '최근 청구서';

    protected static ?int $sort = 3;

    protected int | string | array $columnSpan = 'full';

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
                    ->label('청구서 번호'),

                Tables\Columns\TextColumn::make('customer.company_name')
                    ->label('고객'),

                Tables\Columns\TextColumn::make('issue_date')
                    ->label('발행일')
                    ->date('Y-m-d'),

                Tables\Columns\TextColumn::make('due_date')
                    ->label('납부기한')
                    ->date('Y-m-d')
                    ->color(fn ($record) => $record->is_overdue ? 'danger' : null),

                Tables\Columns\TextColumn::make('total_amount')
                    ->label('금액')
                    ->money('KRW'),

                Tables\Columns\TextColumn::make('status')
                    ->label('상태')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        '초안' => 'gray',
                        '발행' => 'info',
                        '부분결제' => 'warning',
                        '결제완료' => 'success',
                        '연체' => 'danger',
                        '취소' => 'gray',
                        default => 'gray',
                    }),
            ])
            ->actions([
                Tables\Actions\Action::make('view')
                    ->label('보기')
                    ->url(fn (Invoice $record): string => InvoiceResource::getUrl('view', ['record' => $record])),
            ])
            ->paginated(false);
    }
}
