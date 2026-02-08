<?php

namespace App\Filament\Resources;

use App\Enums\ActiveStatus;
use App\Enums\SupplierPaymentTerms;
use App\Filament\Resources\SupplierResource\Pages;
use App\Models\Supplier;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use App\Filament\Traits\HasResourcePermissions;

class SupplierResource extends Resource
{
    use HasResourcePermissions;

    protected static ?string $permissionPrefix = 'supplier';

    protected static ?string $model = Supplier::class;

    protected static ?string $navigationIcon = 'heroicon-o-truck';

    protected static ?int $navigationSort = 1;

    public static function getNavigationGroup(): ?string
    {
        return __('navigation.groups.purchasing');
    }

    public static function getNavigationLabel(): string
    {
        return __('navigation.labels.supplier');
    }

    public static function getModelLabel(): string
    {
        return __('models.supplier');
    }

    public static function getPluralModelLabel(): string
    {
        return __('models.supplier_plural');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(__('common.sections.company_info'))
                    ->schema([
                        Forms\Components\TextInput::make('company_name')
                            ->label(__('fields.company_name'))
                            ->required()
                            ->maxLength(255),

                        Forms\Components\TextInput::make('code')
                            ->label(__('fields.code'))
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(50),

                        Forms\Components\TextInput::make('business_number')
                            ->label(__('fields.business_number'))
                            ->mask('999-99-99999')
                            ->maxLength(12),

                        Forms\Components\TextInput::make('representative')
                            ->label(__('fields.representative'))
                            ->maxLength(100),

                        Forms\Components\TextInput::make('contact_name')
                            ->label(__('fields.contact_name'))
                            ->maxLength(100),
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

                        Forms\Components\Textarea::make('address')
                            ->label(__('fields.address'))
                            ->rows(2)
                            ->columnSpanFull(),
                    ])->columns(3),

                Forms\Components\Section::make(__('common.sections.payment_info'))
                    ->schema([
                        Forms\Components\TextInput::make('bank_name')
                            ->label(__('fields.bank_name'))
                            ->maxLength(50),

                        Forms\Components\TextInput::make('bank_account')
                            ->label(__('fields.bank_account'))
                            ->maxLength(50),

                        Forms\Components\TextInput::make('bank_holder')
                            ->label(__('fields.bank_holder'))
                            ->maxLength(50),

                        Forms\Components\Select::make('payment_terms')
                            ->label(__('fields.payment_terms'))
                            ->options(SupplierPaymentTerms::class)
                            ->default(SupplierPaymentTerms::Postpaid),

                        Forms\Components\TextInput::make('payment_days')
                            ->label(__('fields.payment_days'))
                            ->numeric()
                            ->suffix(__('common.general.days_suffix'))
                            ->default(30),

                        Forms\Components\Select::make('status')
                            ->label(__('fields.status'))
                            ->options(ActiveStatus::class)
                            ->default(ActiveStatus::Active),
                    ])->columns(3),

                Forms\Components\Section::make(__('common.sections.note'))
                    ->schema([
                        Forms\Components\Textarea::make('note')
                            ->label(__('fields.memo'))
                            ->rows(3),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('code')
                    ->label(__('fields.code'))
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('company_name')
                    ->label(__('fields.company_name'))
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('contact_name')
                    ->label(__('fields.contact_name')),

                Tables\Columns\TextColumn::make('phone')
                    ->label(__('fields.phone')),

                Tables\Columns\TextColumn::make('email')
                    ->label(__('fields.email')),

                Tables\Columns\TextColumn::make('payment_terms')
                    ->label(__('fields.payment_terms'))
                    ->badge(),

                Tables\Columns\TextColumn::make('status')
                    ->label(__('fields.status'))
                    ->badge()
                    ->color(fn ($state) => $state?->color() ?? 'gray'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label(__('fields.status'))
                    ->options(ActiveStatus::class),
            ])
            ->recordUrl(null)
            ->recordAction('selectRecord')
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make(__('common.sections.supplier_info'))
                    ->schema([
                        Infolists\Components\TextEntry::make('code')
                            ->label(__('fields.code')),
                        Infolists\Components\TextEntry::make('company_name')
                            ->label(__('fields.company_name')),
                        Infolists\Components\TextEntry::make('contact_name')
                            ->label(__('fields.contact_name')),
                        Infolists\Components\TextEntry::make('phone')
                            ->label(__('fields.phone')),
                        Infolists\Components\TextEntry::make('email')
                            ->label(__('fields.email')),
                        Infolists\Components\TextEntry::make('status')
                            ->label(__('fields.status'))
                            ->badge()
                            ->color(fn ($state) => $state?->color() ?? 'gray'),
                        Infolists\Components\TextEntry::make('payment_terms')
                            ->label(__('fields.payment_terms'))
                            ->badge(),
                        Infolists\Components\TextEntry::make('address')
                            ->label(__('fields.address'))
                            ->columnSpanFull(),
                    ])->columns(2),
                Infolists\Components\Section::make(__('common.sections.payment_info'))
                    ->schema([
                        Infolists\Components\TextEntry::make('bank_name')
                            ->label(__('fields.bank_name')),
                        Infolists\Components\TextEntry::make('bank_account')
                            ->label(__('fields.bank_account')),
                        Infolists\Components\TextEntry::make('bank_holder')
                            ->label(__('fields.bank_holder')),
                        Infolists\Components\TextEntry::make('payment_days')
                            ->label(__('fields.payment_days'))
                            ->suffix(__('common.general.days_suffix')),
                    ])->columns(2),
                Infolists\Components\Section::make(__('common.sections.note'))
                    ->schema([
                        Infolists\Components\TextEntry::make('note')
                            ->label(__('fields.memo'))
                            ->columnSpanFull(),
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
            'index' => Pages\ListSuppliers::route('/'),
            'create' => Pages\CreateSupplier::route('/create'),
            'edit' => Pages\EditSupplier::route('/{record}/edit'),
        ];
    }
}
