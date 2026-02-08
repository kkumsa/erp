<?php

namespace App\Filament\Resources;

use App\Enums\ActiveStatus;
use App\Enums\CustomerType;
use App\Filament\Resources\CustomerResource\Pages;
use App\Filament\Resources\CustomerResource\RelationManagers;
use App\Models\Customer;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use App\Filament\Traits\HasResourcePermissions;

class CustomerResource extends Resource
{
    use HasResourcePermissions;

    protected static ?string $permissionPrefix = 'customer';

    protected static ?string $model = Customer::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office';

    protected static ?int $navigationSort = 1;

    public static function getNavigationGroup(): ?string
    {
        return __('navigation.groups.crm');
    }

    public static function getNavigationLabel(): string
    {
        return __('navigation.labels.customer');
    }

    public static function getModelLabel(): string
    {
        return __('models.customer');
    }

    public static function getPluralModelLabel(): string
    {
        return __('models.customer_plural');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(__('common.sections.customer_info'))
                    ->schema([
                        Forms\Components\TextInput::make('company_name')
                            ->label(__('fields.company_name'))
                            ->required()
                            ->maxLength(255),

                        Forms\Components\TextInput::make('business_number')
                            ->label(__('fields.business_number'))
                            ->unique(ignoreRecord: true)
                            ->mask('999-99-99999')
                            ->maxLength(12),

                        Forms\Components\TextInput::make('representative')
                            ->label(__('fields.representative'))
                            ->maxLength(100),

                        Forms\Components\TextInput::make('industry')
                            ->label(__('fields.industry'))
                            ->maxLength(100),

                        Forms\Components\TextInput::make('business_type')
                            ->label(__('fields.business_type'))
                            ->maxLength(100),

                        Forms\Components\Select::make('assigned_to')
                            ->label(__('fields.assigned_to'))
                            ->relationship('assignedUser', 'name')
                            ->searchable()
                            ->preload(),
                    ])->columns(2),

                Forms\Components\Section::make(__('common.sections.contact_info'))
                    ->schema([
                        Forms\Components\TextInput::make('phone')
                            ->label(__('fields.phone'))
                            ->tel()
                            ->maxLength(20),

                        Forms\Components\TextInput::make('fax')
                            ->label(__('fields.fax'))
                            ->tel()
                            ->maxLength(20),

                        Forms\Components\TextInput::make('email')
                            ->label(__('fields.email'))
                            ->email()
                            ->maxLength(255),

                        Forms\Components\TextInput::make('website')
                            ->label(__('fields.website'))
                            ->url()
                            ->maxLength(255),

                        Forms\Components\Textarea::make('address')
                            ->label(__('fields.address'))
                            ->rows(2)
                            ->columnSpanFull(),
                    ])->columns(2),

                Forms\Components\Section::make(__('common.sections.classification'))
                    ->schema([
                        Forms\Components\Select::make('type')
                            ->label(__('fields.type'))
                            ->options(CustomerType::class)
                            ->default(CustomerType::Prospect)
                            ->required(),

                        Forms\Components\Select::make('status')
                            ->label(__('fields.status'))
                            ->options(ActiveStatus::class)
                            ->default(ActiveStatus::Active)
                            ->required(),

                        Forms\Components\Textarea::make('note')
                            ->label(__('fields.memo'))
                            ->rows(3)
                            ->columnSpanFull(),
                    ])->columns(2),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make(__('common.sections.customer_info'))
                    ->id('customer-info')
                    ->description(fn ($record) => $record->company_name)
                    ->schema([
                        Infolists\Components\TextEntry::make('company_name')
                            ->label(__('fields.company_name')),

                        Infolists\Components\TextEntry::make('business_number')
                            ->label(__('fields.business_number')),

                        Infolists\Components\TextEntry::make('representative')
                            ->label(__('fields.representative')),

                        Infolists\Components\TextEntry::make('industry')
                            ->label(__('fields.industry')),

                        Infolists\Components\TextEntry::make('business_type')
                            ->label(__('fields.business_type')),

                        Infolists\Components\TextEntry::make('assignedUser.name')
                            ->label(__('fields.assigned_to')),
                    ])
                    ->columns(2)
                    ->collapsible()
                    ->persistCollapsed(),

                Infolists\Components\Section::make(__('common.sections.contact_info'))
                    ->id('customer-contact')
                    ->description(fn ($record) => $record->phone ?? '-')
                    ->schema([
                        Infolists\Components\TextEntry::make('phone')
                            ->label(__('fields.phone')),

                        Infolists\Components\TextEntry::make('fax')
                            ->label(__('fields.fax')),

                        Infolists\Components\TextEntry::make('email')
                            ->label(__('fields.email')),

                        Infolists\Components\TextEntry::make('website')
                            ->label(__('fields.website'))
                            ->url(fn ($record) => $record->website),

                        Infolists\Components\TextEntry::make('address')
                            ->label(__('fields.address'))
                            ->columnSpanFull(),
                    ])
                    ->columns(2)
                    ->collapsible()
                    ->persistCollapsed(),

                Infolists\Components\Section::make(__('common.sections.classification'))
                    ->id('customer-category')
                    ->description(fn ($record) => $record->type instanceof CustomerType ? $record->type->getLabel() : ($record->type ?? null))
                    ->schema([
                        Infolists\Components\TextEntry::make('type')
                            ->label(__('fields.type'))
                            ->badge()
                            ->color(fn ($state) => $state instanceof CustomerType ? $state->color() : (CustomerType::tryFrom($state)?->color() ?? 'gray')),

                        Infolists\Components\TextEntry::make('status')
                            ->label(__('fields.status'))
                            ->badge()
                            ->color(fn ($state) => $state instanceof ActiveStatus ? $state->color() : (ActiveStatus::tryFrom($state)?->color() ?? 'gray')),

                        Infolists\Components\TextEntry::make('note')
                            ->label(__('fields.memo'))
                            ->columnSpanFull(),
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
                Tables\Columns\TextColumn::make('company_name')
                    ->label(__('fields.company_name'))
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('business_number')
                    ->label(__('fields.business_number'))
                    ->searchable(),

                Tables\Columns\TextColumn::make('representative')
                    ->label(__('fields.representative')),

                Tables\Columns\TextColumn::make('phone')
                    ->label(__('fields.phone')),

                Tables\Columns\TextColumn::make('type')
                    ->label(__('fields.type'))
                    ->badge()
                    ->color(fn ($state) => $state instanceof CustomerType ? $state->color() : (CustomerType::tryFrom($state)?->color() ?? 'gray')),

                Tables\Columns\TextColumn::make('assignedUser.name')
                    ->label(__('fields.assigned_to')),

                Tables\Columns\TextColumn::make('status')
                    ->label(__('fields.status'))
                    ->badge()
                    ->color(fn ($state) => $state instanceof ActiveStatus ? $state->color() : (ActiveStatus::tryFrom($state)?->color() ?? 'gray')),

                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('fields.created_at'))
                    ->date('Y.m.d')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->label(__('fields.type'))
                    ->options(CustomerType::class),

                Tables\Filters\SelectFilter::make('status')
                    ->label(__('fields.status'))
                    ->options(ActiveStatus::class),

                Tables\Filters\SelectFilter::make('assigned_to')
                    ->label(__('fields.assigned_to'))
                    ->relationship('assignedUser', 'name'),
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
            RelationManagers\ContactsRelationManager::class,
            RelationManagers\OpportunitiesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCustomers::route('/'),
            'create' => Pages\CreateCustomer::route('/create'),
            'view' => Pages\ViewCustomer::route('/{record}'),
            'edit' => Pages\EditCustomer::route('/{record}/edit'),
        ];
    }
}
