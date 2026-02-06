<?php

namespace App\Filament\Resources;

use App\Filament\Resources\InvoiceResource\Pages;
use App\Filament\Resources\InvoiceResource\RelationManagers;
use App\Models\Invoice;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class InvoiceResource extends Resource
{
    protected static ?string $model = Invoice::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationGroup = '재무/회계';

    protected static ?string $navigationLabel = '청구서';

    protected static ?string $modelLabel = '청구서';

    protected static ?string $pluralModelLabel = '청구서';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('청구서 정보')
                    ->schema([
                        Forms\Components\TextInput::make('invoice_number')
                            ->label('청구서 번호')
                            ->disabled()
                            ->dehydrated(false)
                            ->placeholder('자동 생성'),

                        Forms\Components\Select::make('customer_id')
                            ->label('고객')
                            ->relationship('customer', 'company_name')
                            ->required()
                            ->searchable()
                            ->preload(),

                        Forms\Components\Select::make('contract_id')
                            ->label('계약')
                            ->relationship('contract', 'title')
                            ->searchable()
                            ->preload(),

                        Forms\Components\Select::make('project_id')
                            ->label('프로젝트')
                            ->relationship('project', 'name')
                            ->searchable()
                            ->preload(),

                        Forms\Components\DatePicker::make('issue_date')
                            ->label('발행일')
                            ->required()
                            ->default(now()),

                        Forms\Components\DatePicker::make('due_date')
                            ->label('납부기한')
                            ->required()
                            ->default(now()->addDays(30)),
                    ])->columns(2),

                Forms\Components\Section::make('상태')
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->label('상태')
                            ->options([
                                '초안' => '초안',
                                '발행' => '발행',
                                '부분결제' => '부분결제',
                                '결제완료' => '결제완료',
                                '연체' => '연체',
                                '취소' => '취소',
                            ])
                            ->default('초안')
                            ->required(),

                        Forms\Components\Placeholder::make('subtotal')
                            ->label('공급가액')
                            ->content(fn ($record) => $record ? number_format($record->subtotal) . '원' : '-'),

                        Forms\Components\Placeholder::make('tax_amount')
                            ->label('세액')
                            ->content(fn ($record) => $record ? number_format($record->tax_amount) . '원' : '-'),

                        Forms\Components\Placeholder::make('total_amount')
                            ->label('합계')
                            ->content(fn ($record) => $record ? number_format($record->total_amount) . '원' : '-'),
                    ])->columns(4),

                Forms\Components\Section::make('비고')
                    ->schema([
                        Forms\Components\Textarea::make('note')
                            ->label('메모')
                            ->rows(2),

                        Forms\Components\Textarea::make('terms')
                            ->label('결제 조건')
                            ->rows(2),
                    ])->columns(2),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('청구서 정보')
                    ->id('invoice-info')
                    ->description(fn ($record) => $record->invoice_number)
                    ->schema([
                        Infolists\Components\TextEntry::make('invoice_number')
                            ->label('청구서 번호'),

                        Infolists\Components\TextEntry::make('customer.company_name')
                            ->label('고객'),

                        Infolists\Components\TextEntry::make('contract.title')
                            ->label('계약'),

                        Infolists\Components\TextEntry::make('project.name')
                            ->label('프로젝트'),

                        Infolists\Components\TextEntry::make('issue_date')
                            ->label('발행일')
                            ->date('Y-m-d'),

                        Infolists\Components\TextEntry::make('due_date')
                            ->label('납부기한')
                            ->date('Y-m-d')
                            ->color(fn ($record) => $record->is_overdue ? 'danger' : null),
                    ])
                    ->columns(2)
                    ->collapsible()
                    ->persistCollapsed(),

                Infolists\Components\Section::make('상태')
                    ->id('invoice-status')
                    ->description(fn ($record) => $record->status)
                    ->schema([
                        Infolists\Components\TextEntry::make('status')
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

                        Infolists\Components\TextEntry::make('subtotal')
                            ->label('공급가액')
                            ->money('KRW'),

                        Infolists\Components\TextEntry::make('tax_amount')
                            ->label('세액')
                            ->money('KRW'),

                        Infolists\Components\TextEntry::make('total_amount')
                            ->label('합계')
                            ->money('KRW'),

                        Infolists\Components\TextEntry::make('paid_amount')
                            ->label('결제액')
                            ->money('KRW'),

                        Infolists\Components\TextEntry::make('balance')
                            ->label('잔액')
                            ->money('KRW')
                            ->state(fn ($record) => $record->total_amount - $record->paid_amount),
                    ])
                    ->columns(3)
                    ->collapsible()
                    ->persistCollapsed(),

                Infolists\Components\Section::make('비고')
                    ->id('invoice-note')
                    ->description(fn ($record) => $record->note ? mb_substr($record->note, 0, 30) . (mb_strlen($record->note) > 30 ? '...' : '') : '-')
                    ->schema([
                        Infolists\Components\TextEntry::make('note')
                            ->label('메모')
                            ->placeholder('-'),

                        Infolists\Components\TextEntry::make('terms')
                            ->label('결제 조건')
                            ->placeholder('-'),
                    ])
                    ->columns(2)
                    ->collapsible()
                    ->persistCollapsed(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('invoice_number')
                    ->label('청구서 번호')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('customer.company_name')
                    ->label('고객')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('issue_date')
                    ->label('발행일')
                    ->date('Y-m-d')
                    ->sortable(),

                Tables\Columns\TextColumn::make('due_date')
                    ->label('납부기한')
                    ->date('Y-m-d')
                    ->sortable()
                    ->color(fn ($record) => $record->is_overdue ? 'danger' : null),

                Tables\Columns\TextColumn::make('total_amount')
                    ->label('합계')
                    ->money('KRW')
                    ->sortable(),

                Tables\Columns\TextColumn::make('paid_amount')
                    ->label('결제액')
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
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('상태')
                    ->options([
                        '초안' => '초안',
                        '발행' => '발행',
                        '부분결제' => '부분결제',
                        '결제완료' => '결제완료',
                        '연체' => '연체',
                        '취소' => '취소',
                    ]),

                Tables\Filters\SelectFilter::make('customer_id')
                    ->label('고객')
                    ->relationship('customer', 'company_name'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\ItemsRelationManager::class,
            RelationManagers\PaymentsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListInvoices::route('/'),
            'create' => Pages\CreateInvoice::route('/create'),
            'view' => Pages\ViewInvoice::route('/{record}'),
            'edit' => Pages\EditInvoice::route('/{record}/edit'),
        ];
    }
}
