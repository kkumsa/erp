<?php

namespace App\Filament\Resources\CustomerResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class ContactsRelationManager extends RelationManager
{
    protected static string $relationship = 'contacts';

    protected static ?string $title = null;

    protected static ?string $modelLabel = null;

    public static function getTitle(\Illuminate\Database\Eloquent\Model $ownerRecord, string $pageClass): string
    {
        return __('models.contact');
    }

    public static function getModelLabel(): string
    {
        return __('models.contact');
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label(__('fields.name'))
                    ->required()
                    ->maxLength(255),

                Forms\Components\TextInput::make('position')
                    ->label(__('fields.position'))
                    ->maxLength(100),

                Forms\Components\TextInput::make('department')
                    ->label(__('fields.department'))
                    ->maxLength(100),

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
                    ->rows(2)
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label(__('fields.name'))
                    ->searchable(),

                Tables\Columns\TextColumn::make('position')
                    ->label(__('fields.position')),

                Tables\Columns\TextColumn::make('department')
                    ->label(__('fields.department')),

                Tables\Columns\TextColumn::make('phone')
                    ->label(__('fields.phone')),

                Tables\Columns\TextColumn::make('email')
                    ->label(__('fields.email')),

                Tables\Columns\IconColumn::make('is_primary')
                    ->label(__('fields.is_primary'))
                    ->boolean(),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
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
}
