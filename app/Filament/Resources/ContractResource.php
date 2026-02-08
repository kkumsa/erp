<?php

namespace App\Filament\Resources;

use App\Enums\ContractPaymentTerms;
use App\Enums\ContractStatus;
use App\Filament\Resources\ContractResource\Pages;
use App\Models\Contract;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use App\Filament\Traits\HasResourcePermissions;

class ContractResource extends Resource
{
    use HasResourcePermissions;

    protected static ?string $permissionPrefix = 'contract';

    protected static ?string $model = Contract::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?int $navigationSort = 3;

    public static function getNavigationGroup(): ?string
    {
        return __('navigation.groups.crm');
    }

    public static function getNavigationLabel(): string
    {
        return __('navigation.labels.contract');
    }

    public static function getModelLabel(): string
    {
        return __('models.contract');
    }

    public static function getPluralModelLabel(): string
    {
        return __('models.contract_plural');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(__('common.sections.contract_info'))
                    ->schema([
                        Forms\Components\TextInput::make('contract_number')
                            ->label(__('fields.contract_number'))
                            ->disabled()
                            ->dehydrated(false)
                            ->placeholder(__('common.placeholders.auto_generated')),

                        Forms\Components\TextInput::make('title')
                            ->label(__('fields.title'))
                            ->required()
                            ->maxLength(255),

                        Forms\Components\Select::make('customer_id')
                            ->label(__('fields.customer_id'))
                            ->relationship('customer', 'company_name')
                            ->searchable()
                            ->preload()
                            ->required(),

                        Forms\Components\Select::make('opportunity_id')
                            ->label(__('fields.opportunity_id'))
                            ->relationship(
                                'opportunity',
                                'name',
                                fn ($query, $get) => $query->where('customer_id', $get('customer_id'))
                            )
                            ->searchable()
                            ->preload()
                            ->visible(fn ($get) => filled($get('customer_id'))),
                    ])->columns(2),

                Forms\Components\Section::make(__('common.sections.contract_terms'))
                    ->schema([
                        Forms\Components\TextInput::make('amount')
                            ->label(__('fields.amount'))
                            ->numeric()
                            ->prefix('₩')
                            ->required(),

                        Forms\Components\Select::make('payment_terms')
                            ->label(__('fields.payment_terms'))
                            ->options(ContractPaymentTerms::class)
                            ->default(ContractPaymentTerms::LumpSum)
                            ->required(),

                        Forms\Components\DatePicker::make('start_date')
                            ->label(__('fields.start_date'))
                            ->required(),

                        Forms\Components\DatePicker::make('end_date')
                            ->label(__('fields.end_date'))
                            ->required()
                            ->after('start_date'),
                    ])->columns(2),

                Forms\Components\Section::make(__('common.sections.contract_status'))
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->label(__('fields.status'))
                            ->options(ContractStatus::class)
                            ->default(ContractStatus::Drafting)
                            ->required(),

                        Forms\Components\Select::make('signed_by')
                            ->label(__('fields.signed_by'))
                            ->relationship('signer', 'name')
                            ->searchable()
                            ->preload(),

                        Forms\Components\DateTimePicker::make('signed_at')
                            ->label(__('fields.signed_at')),

                        Forms\Components\FileUpload::make('file_path')
                            ->label(__('fields.file_path'))
                            ->directory('contracts')
                            ->acceptedFileTypes(['application/pdf'])
                            ->maxSize(10240),
                    ])->columns(2),

                Forms\Components\Section::make(__('common.sections.detail_content'))
                    ->schema([
                        Forms\Components\Textarea::make('description')
                            ->label(__('fields.description'))
                            ->rows(4)
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make(__('common.sections.contract_info'))
                    ->id('contract-info')
                    ->description(fn ($record) => $record->contract_number)
                    ->schema([
                        Infolists\Components\TextEntry::make('contract_number')
                            ->label(__('fields.contract_number')),

                        Infolists\Components\TextEntry::make('title')
                            ->label(__('fields.title')),

                        Infolists\Components\TextEntry::make('customer.company_name')
                            ->label(__('fields.customer_id')),

                        Infolists\Components\TextEntry::make('opportunity.name')
                            ->label(__('fields.opportunity_id'))
                            ->placeholder('-'),
                    ])
                    ->columns(2)
                    ->collapsible()
                    ->persistCollapsed(),

                Infolists\Components\Section::make(__('common.sections.contract_terms'))
                    ->id('contract-terms')
                    ->description(fn ($record) => number_format($record->amount) . __('common.general.won'))
                    ->schema([
                        Infolists\Components\TextEntry::make('amount')
                            ->label(__('fields.amount'))
                            ->money('KRW'),

                        Infolists\Components\TextEntry::make('payment_terms')
                            ->label(__('fields.payment_terms'))
                            ->badge(),

                        Infolists\Components\TextEntry::make('start_date')
                            ->label(__('fields.start_date'))
                            ->date('Y.m.d'),

                        Infolists\Components\TextEntry::make('end_date')
                            ->label(__('fields.end_date'))
                            ->date('Y.m.d'),

                        Infolists\Components\TextEntry::make('duration_months')
                            ->label(__('fields.duration_months'))
                            ->suffix(__('common.general.months_suffix')),
                    ])
                    ->columns(2)
                    ->collapsible()
                    ->persistCollapsed(),

                Infolists\Components\Section::make(__('common.sections.contract_status'))
                    ->id('contract-status')
                    ->description(fn ($record) => $record->status instanceof ContractStatus ? $record->status->getLabel() : ($record->status ?? null))
                    ->schema([
                        Infolists\Components\TextEntry::make('status')
                            ->label(__('fields.status'))
                            ->badge()
                            ->color(fn ($state) => $state instanceof ContractStatus ? $state->color() : (ContractStatus::tryFrom($state)?->color() ?? 'gray')),

                        Infolists\Components\TextEntry::make('signer.name')
                            ->label(__('fields.signed_by'))
                            ->placeholder('-'),

                        Infolists\Components\TextEntry::make('signed_at')
                            ->label(__('fields.signed_at'))
                            ->dateTime('Y.m.d H:i')
                            ->placeholder('-'),

                        Infolists\Components\TextEntry::make('is_expired')
                            ->label(__('fields.is_expired'))
                            ->formatStateUsing(fn ($state) => $state ? __('common.statuses.expired') : __('common.statuses.valid'))
                            ->badge()
                            ->color(fn ($state) => $state ? 'danger' : 'success'),

                        Infolists\Components\TextEntry::make('description')
                            ->label(__('fields.description'))
                            ->columnSpanFull(),

                        Infolists\Components\TextEntry::make('created_at')
                            ->label(__('fields.created_at'))
                            ->dateTime('Y.m.d H:i'),

                        Infolists\Components\TextEntry::make('updated_at')
                            ->label(__('fields.updated_at'))
                            ->dateTime('Y.m.d H:i'),
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
                Tables\Columns\TextColumn::make('contract_number')
                    ->label(__('fields.contract_number'))
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('title')
                    ->label(__('fields.title'))
                    ->searchable()
                    ->sortable()
                    ->limit(30),

                Tables\Columns\TextColumn::make('customer.company_name')
                    ->label(__('fields.customer_id'))
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('amount')
                    ->label(__('fields.amount'))
                    ->money('KRW')
                    ->sortable(),

                Tables\Columns\TextColumn::make('status')
                    ->label(__('fields.status'))
                    ->badge()
                    ->color(fn ($state) => $state instanceof ContractStatus ? $state->color() : (ContractStatus::tryFrom($state)?->color() ?? 'gray')),

                Tables\Columns\TextColumn::make('start_date')
                    ->label(__('fields.start_date'))
                    ->date('Y.m.d')
                    ->sortable(),

                Tables\Columns\TextColumn::make('end_date')
                    ->label(__('fields.end_date'))
                    ->date('Y.m.d')
                    ->sortable(),

                Tables\Columns\TextColumn::make('signer.name')
                    ->label(__('fields.signed_by'))
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('fields.created_at'))
                    ->date('Y.m.d')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label(__('fields.status'))
                    ->options(ContractStatus::class),

                Tables\Filters\SelectFilter::make('customer_id')
                    ->label(__('fields.customer_id'))
                    ->relationship('customer', 'company_name')
                    ->searchable()
                    ->preload(),

                Tables\Filters\SelectFilter::make('payment_terms')
                    ->label(__('fields.payment_terms'))
                    ->options(ContractPaymentTerms::class),
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListContracts::route('/'),
            'create' => Pages\CreateContract::route('/create'),
            'view' => Pages\ViewContract::route('/{record}'),
            'edit' => Pages\EditContract::route('/{record}/edit'),
        ];
    }
}
