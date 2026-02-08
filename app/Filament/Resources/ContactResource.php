<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ContactResource\Pages;
use App\Models\Contact;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use App\Filament\Traits\HasResourcePermissions;

class ContactResource extends Resource
{
    use HasResourcePermissions;

    protected static ?string $permissionPrefix = 'contact';

    protected static ?string $model = Contact::class;

    protected static ?string $navigationIcon = 'heroicon-o-phone';

    protected static ?int $navigationSort = 5;

    public static function getNavigationGroup(): ?string
    {
        return __('navigation.groups.crm');
    }

    public static function getNavigationLabel(): string
    {
        return __('navigation.labels.contact');
    }

    public static function getModelLabel(): string
    {
        return __('models.contact');
    }

    public static function getPluralModelLabel(): string
    {
        return __('models.contact_plural');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('customer_id')
                    ->label(__('fields.customer_id'))
                    ->relationship('customer', 'company_name')
                    ->required()
                    ->searchable()
                    ->preload(),

                Forms\Components\TextInput::make('name')
                    ->label(__('fields.name'))
                    ->required()
                    ->maxLength(255),

                Forms\Components\TextInput::make('position')
                    ->label(__('fields.position'))
                    ->maxLength(255),

                Forms\Components\TextInput::make('department')
                    ->label(__('fields.department'))
                    ->maxLength(255),

                Forms\Components\TextInput::make('phone')
                    ->label(__('fields.phone'))
                    ->tel()
                    ->maxLength(20),

                Forms\Components\TextInput::make('mobile')
                    ->label(__('fields.mobile'))
                    ->tel()
                    ->maxLength(20),

                Forms\Components\TextInput::make('email')
                    ->label(__('fields.email'))
                    ->email()
                    ->maxLength(255),

                Forms\Components\Toggle::make('is_primary')
                    ->label(__('fields.is_primary')),

                Forms\Components\Textarea::make('note')
                    ->label(__('fields.memo'))
                    ->rows(4)
                    ->columnSpanFull(),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make(__('common.sections.contact_info'))
                    ->id('contact-info')
                    ->description(fn ($record) => $record->name)
                    ->schema([
                        Infolists\Components\TextEntry::make('name')
                            ->label(__('fields.name')),

                        Infolists\Components\TextEntry::make('customer.company_name')
                            ->label(__('fields.customer_id')),

                        Infolists\Components\TextEntry::make('position')
                            ->label(__('fields.position')),

                        Infolists\Components\TextEntry::make('department')
                            ->label(__('fields.department')),

                        Infolists\Components\TextEntry::make('phone')
                            ->label(__('fields.phone')),

                        Infolists\Components\TextEntry::make('mobile')
                            ->label(__('fields.mobile')),

                        Infolists\Components\TextEntry::make('email')
                            ->label(__('fields.email')),

                        Infolists\Components\IconEntry::make('is_primary')
                            ->label(__('fields.is_primary'))
                            ->boolean(),

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
                Tables\Columns\TextColumn::make('name')
                    ->label(__('fields.name'))
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('customer.company_name')
                    ->label(__('fields.customer_id'))
                    ->sortable(),

                Tables\Columns\TextColumn::make('position')
                    ->label(__('fields.position')),

                Tables\Columns\TextColumn::make('email')
                    ->label(__('fields.email')),

                Tables\Columns\TextColumn::make('phone')
                    ->label(__('fields.phone')),

                Tables\Columns\IconColumn::make('is_primary')
                    ->label(__('fields.is_primary'))
                    ->boolean(),
            ])
            ->defaultSort('created_at', 'desc')
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
            'index' => Pages\ListContacts::route('/'),
            'create' => Pages\CreateContact::route('/create'),
            'edit' => Pages\EditContact::route('/{record}/edit'),
        ];
    }
}
