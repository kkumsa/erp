<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SupplierResource\Pages;
use App\Models\Supplier;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class SupplierResource extends Resource
{
    protected static ?string $model = Supplier::class;

    protected static ?string $navigationIcon = 'heroicon-o-truck';

    protected static ?string $navigationGroup = '구매관리';

    protected static ?string $navigationLabel = '공급업체';

    protected static ?string $modelLabel = '공급업체';

    protected static ?string $pluralModelLabel = '공급업체';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('기업 정보')
                    ->schema([
                        Forms\Components\TextInput::make('company_name')
                            ->label('회사명')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\TextInput::make('code')
                            ->label('공급업체 코드')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(50),

                        Forms\Components\TextInput::make('business_number')
                            ->label('사업자번호')
                            ->mask('999-99-99999')
                            ->maxLength(12),

                        Forms\Components\TextInput::make('representative')
                            ->label('대표자')
                            ->maxLength(100),

                        Forms\Components\TextInput::make('contact_name')
                            ->label('담당자')
                            ->maxLength(100),
                    ])->columns(2),

                Forms\Components\Section::make('연락처')
                    ->schema([
                        Forms\Components\TextInput::make('phone')
                            ->label('전화번호')
                            ->tel()
                            ->maxLength(20),

                        Forms\Components\TextInput::make('fax')
                            ->label('팩스')
                            ->tel()
                            ->maxLength(20),

                        Forms\Components\TextInput::make('email')
                            ->label('이메일')
                            ->email()
                            ->maxLength(255),

                        Forms\Components\Textarea::make('address')
                            ->label('주소')
                            ->rows(2)
                            ->columnSpanFull(),
                    ])->columns(3),

                Forms\Components\Section::make('결제 정보')
                    ->schema([
                        Forms\Components\TextInput::make('bank_name')
                            ->label('은행명')
                            ->maxLength(50),

                        Forms\Components\TextInput::make('bank_account')
                            ->label('계좌번호')
                            ->maxLength(50),

                        Forms\Components\TextInput::make('bank_holder')
                            ->label('예금주')
                            ->maxLength(50),

                        Forms\Components\Select::make('payment_terms')
                            ->label('결제 조건')
                            ->options([
                                '선불' => '선불',
                                '후불' => '후불',
                                '정산' => '정산',
                            ])
                            ->default('후불'),

                        Forms\Components\TextInput::make('payment_days')
                            ->label('결제 기한')
                            ->numeric()
                            ->suffix('일')
                            ->default(30),

                        Forms\Components\Select::make('status')
                            ->label('상태')
                            ->options([
                                '활성' => '활성',
                                '비활성' => '비활성',
                            ])
                            ->default('활성'),
                    ])->columns(3),

                Forms\Components\Section::make('비고')
                    ->schema([
                        Forms\Components\Textarea::make('note')
                            ->label('메모')
                            ->rows(3),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('code')
                    ->label('코드')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('company_name')
                    ->label('회사명')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('contact_name')
                    ->label('담당자'),

                Tables\Columns\TextColumn::make('phone')
                    ->label('전화번호'),

                Tables\Columns\TextColumn::make('email')
                    ->label('이메일'),

                Tables\Columns\TextColumn::make('payment_terms')
                    ->label('결제 조건')
                    ->badge(),

                Tables\Columns\TextColumn::make('status')
                    ->label('상태')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        '활성' => 'success',
                        '비활성' => 'gray',
                        default => 'gray',
                    }),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('상태')
                    ->options([
                        '활성' => '활성',
                        '비활성' => '비활성',
                    ]),
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
