<?php

namespace App\Filament\Resources\InvoiceResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class PaymentsRelationManager extends RelationManager
{
    protected static string $relationship = 'payments';

    protected static ?string $title = '결제 내역';

    protected static ?string $modelLabel = '결제';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\DatePicker::make('payment_date')
                    ->label('결제일')
                    ->required()
                    ->default(now()),

                Forms\Components\TextInput::make('amount')
                    ->label('금액')
                    ->numeric()
                    ->required()
                    ->prefix('₩'),

                Forms\Components\Select::make('method')
                    ->label('결제 방법')
                    ->options([
                        '현금' => '현금',
                        '카드' => '카드',
                        '계좌이체' => '계좌이체',
                        '어음' => '어음',
                        '기타' => '기타',
                    ])
                    ->required()
                    ->default('계좌이체'),

                Forms\Components\TextInput::make('reference')
                    ->label('참조번호')
                    ->maxLength(100),

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
                Tables\Columns\TextColumn::make('payment_number')
                    ->label('결제번호'),

                Tables\Columns\TextColumn::make('payment_date')
                    ->label('결제일')
                    ->date('Y-m-d'),

                Tables\Columns\TextColumn::make('amount')
                    ->label('금액')
                    ->money('KRW'),

                Tables\Columns\TextColumn::make('method')
                    ->label('결제 방법')
                    ->badge(),

                Tables\Columns\TextColumn::make('reference')
                    ->label('참조번호'),

                Tables\Columns\TextColumn::make('recorder.name')
                    ->label('등록자'),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['recorded_by'] = auth()->id();
                        return $data;
                    }),
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
