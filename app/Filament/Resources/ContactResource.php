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

    protected static ?string $navigationGroup = 'CRM';

    protected static ?string $navigationLabel = '연락처';

    protected static ?string $modelLabel = '연락처';

    protected static ?string $pluralModelLabel = '연락처';

    protected static ?int $navigationSort = 5;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('customer_id')
                    ->label('고객사')
                    ->relationship('customer', 'company_name')
                    ->required()
                    ->searchable()
                    ->preload(),

                Forms\Components\TextInput::make('name')
                    ->label('이름')
                    ->required()
                    ->maxLength(255),

                Forms\Components\TextInput::make('position')
                    ->label('직책')
                    ->maxLength(255),

                Forms\Components\TextInput::make('department')
                    ->label('부서')
                    ->maxLength(255),

                Forms\Components\TextInput::make('phone')
                    ->label('전화')
                    ->tel()
                    ->maxLength(20),

                Forms\Components\TextInput::make('mobile')
                    ->label('휴대폰')
                    ->tel()
                    ->maxLength(20),

                Forms\Components\TextInput::make('email')
                    ->label('이메일')
                    ->email()
                    ->maxLength(255),

                Forms\Components\Toggle::make('is_primary')
                    ->label('주요 연락처'),

                Forms\Components\Textarea::make('note')
                    ->label('메모')
                    ->rows(4)
                    ->columnSpanFull(),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('연락처 정보')
                    ->id('contact-info')
                    ->description(fn ($record) => $record->name)
                    ->schema([
                        Infolists\Components\TextEntry::make('name')
                            ->label('이름'),

                        Infolists\Components\TextEntry::make('customer.company_name')
                            ->label('고객사'),

                        Infolists\Components\TextEntry::make('position')
                            ->label('직책'),

                        Infolists\Components\TextEntry::make('department')
                            ->label('부서'),

                        Infolists\Components\TextEntry::make('phone')
                            ->label('전화'),

                        Infolists\Components\TextEntry::make('mobile')
                            ->label('휴대폰'),

                        Infolists\Components\TextEntry::make('email')
                            ->label('이메일'),

                        Infolists\Components\IconEntry::make('is_primary')
                            ->label('주요 연락처')
                            ->boolean(),

                        Infolists\Components\TextEntry::make('note')
                            ->label('메모')
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
                    ->label('이름')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('customer.company_name')
                    ->label('고객사')
                    ->sortable(),

                Tables\Columns\TextColumn::make('position')
                    ->label('직책'),

                Tables\Columns\TextColumn::make('email')
                    ->label('이메일'),

                Tables\Columns\TextColumn::make('phone')
                    ->label('전화'),

                Tables\Columns\IconColumn::make('is_primary')
                    ->label('주요 연락처')
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
