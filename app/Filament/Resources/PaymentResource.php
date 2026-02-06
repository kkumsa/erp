<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PaymentResource\Pages;
use App\Models\Payment;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use App\Filament\Traits\HasResourcePermissions;

class PaymentResource extends Resource
{
    use HasResourcePermissions;

    protected static ?string $permissionPrefix = 'payment';

    protected static ?string $model = Payment::class;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';

    protected static ?string $navigationGroup = '재무/회계';

    protected static ?string $navigationLabel = '결제 관리';

    protected static ?string $modelLabel = '결제';

    protected static ?string $pluralModelLabel = '결제';

    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('결제 정보')
                    ->schema([
                        Forms\Components\DatePicker::make('payment_date')
                            ->label('결제일')
                            ->required()
                            ->default(now()),

                        Forms\Components\TextInput::make('amount')
                            ->label('금액')
                            ->numeric()
                            ->prefix('₩')
                            ->required(),

                        Forms\Components\Select::make('method')
                            ->label('결제 방법')
                            ->options([
                                '계좌이체' => '계좌이체',
                                '현금' => '현금',
                                '카드' => '카드',
                                '수표' => '수표',
                                '기타' => '기타',
                            ])
                            ->required(),

                        Forms\Components\TextInput::make('reference')
                            ->label('참조번호')
                            ->maxLength(255),

                        Forms\Components\Textarea::make('note')
                            ->label('메모')
                            ->rows(3)
                            ->columnSpanFull(),
                    ])->columns(2),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('결제 정보')
                    ->id('payment-info')
                    ->description(fn ($record) => $record->payment_number)
                    ->schema([
                        Infolists\Components\TextEntry::make('payment_number')
                            ->label('결제번호'),

                        Infolists\Components\TextEntry::make('payment_date')
                            ->label('결제일')
                            ->date('Y-m-d'),

                        Infolists\Components\TextEntry::make('amount')
                            ->label('금액')
                            ->money('KRW'),

                        Infolists\Components\TextEntry::make('method')
                            ->label('결제 방법')
                            ->badge(),

                        Infolists\Components\TextEntry::make('reference')
                            ->label('참조번호')
                            ->placeholder('-'),

                        Infolists\Components\TextEntry::make('recorder.name')
                            ->label('등록자')
                            ->placeholder('-'),

                        Infolists\Components\TextEntry::make('note')
                            ->label('메모')
                            ->placeholder('-')
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
                Tables\Columns\TextColumn::make('payment_number')
                    ->label('결제번호')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('payment_date')
                    ->label('결제일')
                    ->date('Y-m-d')
                    ->sortable(),

                Tables\Columns\TextColumn::make('amount')
                    ->label('금액')
                    ->money('KRW')
                    ->sortable(),

                Tables\Columns\TextColumn::make('method')
                    ->label('결제 방법')
                    ->badge(),

                Tables\Columns\TextColumn::make('recorder.name')
                    ->label('등록자'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('등록일')
                    ->dateTime('Y-m-d')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('method')
                    ->label('결제 방법')
                    ->options([
                        '계좌이체' => '계좌이체',
                        '현금' => '현금',
                        '카드' => '카드',
                        '수표' => '수표',
                        '기타' => '기타',
                    ]),
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

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPayments::route('/'),
            'create' => Pages\CreatePayment::route('/create'),
            'edit' => Pages\EditPayment::route('/{record}/edit'),
        ];
    }
}
