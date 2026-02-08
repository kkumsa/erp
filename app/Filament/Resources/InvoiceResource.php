<?php

namespace App\Filament\Resources;

use App\Enums\InvoiceStatus;
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
use App\Filament\Traits\HasResourcePermissions;

class InvoiceResource extends Resource
{
    use HasResourcePermissions;

    protected static ?string $permissionPrefix = 'invoice';

    protected static ?string $model = Invoice::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?int $navigationSort = 2;

    public static function getNavigationGroup(): ?string
    {
        return __('navigation.groups.finance');
    }

    public static function getNavigationLabel(): string
    {
        return __('navigation.labels.invoice');
    }

    public static function getModelLabel(): string
    {
        return __('models.invoice');
    }

    public static function getPluralModelLabel(): string
    {
        return __('models.invoice_plural');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(__('common.sections.invoice_info'))
                    ->schema([
                        Forms\Components\TextInput::make('invoice_number')
                            ->label(__('fields.invoice_number'))
                            ->disabled()
                            ->dehydrated(false)
                            ->placeholder(__('common.placeholders.auto_generated')),

                        Forms\Components\Select::make('customer_id')
                            ->label(__('fields.customer_id'))
                            ->relationship('customer', 'company_name')
                            ->required()
                            ->searchable()
                            ->preload(),

                        Forms\Components\Select::make('contract_id')
                            ->label(__('fields.contract_id'))
                            ->relationship('contract', 'title')
                            ->searchable()
                            ->preload(),

                        Forms\Components\Select::make('project_id')
                            ->label(__('fields.project_id'))
                            ->relationship('project', 'name')
                            ->searchable()
                            ->preload(),

                        Forms\Components\DatePicker::make('issue_date')
                            ->label(__('fields.issue_date'))
                            ->required()
                            ->default(now()),

                        Forms\Components\DatePicker::make('due_date')
                            ->label(__('fields.due_date'))
                            ->required()
                            ->default(now()->addDays(30)),
                    ])->columns(2),

                Forms\Components\Section::make(__('fields.status'))
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->label(__('fields.status'))
                            ->options(InvoiceStatus::class)
                            ->default(InvoiceStatus::Draft)
                            ->required(),

                        Forms\Components\Placeholder::make('subtotal')
                            ->label(__('fields.subtotal'))
                            ->content(fn ($record) => $record ? number_format($record->subtotal) . __('common.general.won') : '-'),

                        Forms\Components\Placeholder::make('tax_amount')
                            ->label(__('fields.tax_amount'))
                            ->content(fn ($record) => $record ? number_format($record->tax_amount) . __('common.general.won') : '-'),

                        Forms\Components\Placeholder::make('total_amount')
                            ->label(__('fields.total_amount'))
                            ->content(fn ($record) => $record ? number_format($record->total_amount) . __('common.general.won') : '-'),
                    ])->columns(4),

                Forms\Components\Section::make(__('common.sections.note'))
                    ->schema([
                        Forms\Components\Textarea::make('note')
                            ->label(__('fields.memo'))
                            ->rows(2),

                        Forms\Components\Textarea::make('terms')
                            ->label(__('fields.terms'))
                            ->rows(2),
                    ])->columns(2),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make(__('common.sections.invoice_info'))
                    ->id('invoice-info')
                    ->description(fn ($record) => $record->invoice_number)
                    ->schema([
                        Infolists\Components\TextEntry::make('invoice_number')
                            ->label(__('fields.invoice_number')),

                        Infolists\Components\TextEntry::make('customer.company_name')
                            ->label(__('fields.customer')),

                        Infolists\Components\TextEntry::make('contract.title')
                            ->label(__('fields.contract')),

                        Infolists\Components\TextEntry::make('project.name')
                            ->label(__('fields.project')),

                        Infolists\Components\TextEntry::make('issue_date')
                            ->label(__('fields.issue_date'))
                            ->date('Y.m.d'),

                        Infolists\Components\TextEntry::make('due_date')
                            ->label(__('fields.due_date'))
                            ->date('Y.m.d')
                            ->color(fn ($record) => $record->is_overdue ? 'danger' : null),
                    ])
                    ->columns(2)
                    ->collapsible()
                    ->persistCollapsed(),

                Infolists\Components\Section::make(__('fields.status'))
                    ->id('invoice-status')
                    ->description(fn ($record) => $record->status?->getLabel())
                    ->schema([
                        Infolists\Components\TextEntry::make('status')
                            ->label(__('fields.status'))
                            ->badge()
                            ->color(fn ($state) => $state?->color() ?? 'gray'),

                        Infolists\Components\TextEntry::make('subtotal')
                            ->label(__('fields.subtotal'))
                            ->money('KRW'),

                        Infolists\Components\TextEntry::make('tax_amount')
                            ->label(__('fields.tax_amount'))
                            ->money('KRW'),

                        Infolists\Components\TextEntry::make('total_amount')
                            ->label(__('fields.total_amount'))
                            ->money('KRW'),

                        Infolists\Components\TextEntry::make('paid_amount')
                            ->label(__('fields.paid_amount'))
                            ->money('KRW'),

                        Infolists\Components\TextEntry::make('balance')
                            ->label(__('fields.balance'))
                            ->money('KRW')
                            ->state(fn ($record) => $record->total_amount - $record->paid_amount),
                    ])
                    ->columns(3)
                    ->collapsible()
                    ->persistCollapsed(),

                Infolists\Components\Section::make(__('common.sections.note'))
                    ->id('invoice-note')
                    ->description(fn ($record) => $record->note ? mb_substr($record->note, 0, 30) . (mb_strlen($record->note) > 30 ? '...' : '') : '-')
                    ->schema([
                        Infolists\Components\TextEntry::make('note')
                            ->label(__('fields.memo'))
                            ->placeholder('-'),

                        Infolists\Components\TextEntry::make('terms')
                            ->label(__('fields.terms'))
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
                    ->label(__('fields.invoice_number'))
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('customer.company_name')
                    ->label(__('fields.customer'))
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('issue_date')
                    ->label(__('fields.issue_date'))
                    ->date('Y.m.d')
                    ->sortable(),

                Tables\Columns\TextColumn::make('due_date')
                    ->label(__('fields.due_date'))
                    ->date('Y.m.d')
                    ->sortable()
                    ->color(fn ($record) => $record->is_overdue ? 'danger' : null),

                Tables\Columns\TextColumn::make('total_amount')
                    ->label(__('fields.total_amount'))
                    ->money('KRW')
                    ->sortable(),

                Tables\Columns\TextColumn::make('paid_amount')
                    ->label(__('fields.paid_amount'))
                    ->money('KRW'),

                Tables\Columns\TextColumn::make('status')
                    ->label(__('fields.status'))
                    ->badge()
                    ->color(fn ($state) => $state?->color() ?? 'gray'),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label(__('fields.status'))
                    ->options(InvoiceStatus::class),

                Tables\Filters\SelectFilter::make('customer_id')
                    ->label(__('fields.customer_id'))
                    ->relationship('customer', 'company_name'),
            ])
            ->recordUrl(null)
            ->recordAction('selectRecord')
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
