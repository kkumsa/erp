<?php

namespace App\Filament\Resources\CustomerResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class OpportunitiesRelationManager extends RelationManager
{
    protected static string $relationship = 'opportunities';

    protected static ?string $title = '영업 기회';

    protected static ?string $modelLabel = '영업 기회';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('기회명')
                    ->required()
                    ->maxLength(255),

                Forms\Components\Select::make('contact_id')
                    ->label('담당자')
                    ->relationship('contact', 'name')
                    ->searchable()
                    ->preload(),

                Forms\Components\TextInput::make('amount')
                    ->label('예상 금액')
                    ->numeric()
                    ->prefix('₩'),

                Forms\Components\Select::make('stage')
                    ->label('단계')
                    ->options([
                        '발굴' => '발굴',
                        '접촉' => '접촉',
                        '제안' => '제안',
                        '협상' => '협상',
                        '계약완료' => '계약완료',
                        '실패' => '실패',
                    ])
                    ->default('발굴')
                    ->required(),

                Forms\Components\TextInput::make('probability')
                    ->label('성공 확률')
                    ->numeric()
                    ->suffix('%')
                    ->default(10),

                Forms\Components\DatePicker::make('expected_close_date')
                    ->label('예상 계약일'),

                Forms\Components\Select::make('assigned_to')
                    ->label('영업 담당자')
                    ->relationship('assignedUser', 'name')
                    ->searchable()
                    ->preload(),

                Forms\Components\Textarea::make('description')
                    ->label('설명')
                    ->rows(2)
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('기회명')
                    ->searchable(),

                Tables\Columns\TextColumn::make('amount')
                    ->label('예상 금액')
                    ->money('KRW')
                    ->sortable(),

                Tables\Columns\TextColumn::make('stage')
                    ->label('단계')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        '발굴' => 'gray',
                        '접촉' => 'info',
                        '제안' => 'warning',
                        '협상' => 'primary',
                        '계약완료' => 'success',
                        '실패' => 'danger',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('probability')
                    ->label('확률')
                    ->suffix('%'),

                Tables\Columns\TextColumn::make('expected_close_date')
                    ->label('예상 계약일')
                    ->date('Y-m-d'),

                Tables\Columns\TextColumn::make('assignedUser.name')
                    ->label('담당자'),
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
