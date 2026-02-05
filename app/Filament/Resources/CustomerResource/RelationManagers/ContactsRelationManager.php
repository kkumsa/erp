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

    protected static ?string $title = '담당자';

    protected static ?string $modelLabel = '담당자';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('이름')
                    ->required()
                    ->maxLength(255),

                Forms\Components\TextInput::make('position')
                    ->label('직책')
                    ->maxLength(100),

                Forms\Components\TextInput::make('department')
                    ->label('부서')
                    ->maxLength(100),

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
                    ->label('대표 담당자'),

                Forms\Components\Textarea::make('note')
                    ->label('메모')
                    ->rows(2)
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('이름')
                    ->searchable(),

                Tables\Columns\TextColumn::make('position')
                    ->label('직책'),

                Tables\Columns\TextColumn::make('department')
                    ->label('부서'),

                Tables\Columns\TextColumn::make('phone')
                    ->label('전화'),

                Tables\Columns\TextColumn::make('email')
                    ->label('이메일'),

                Tables\Columns\IconColumn::make('is_primary')
                    ->label('대표')
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
