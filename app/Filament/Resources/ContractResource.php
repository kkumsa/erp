<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ContractResource\Pages;
use App\Models\Contract;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ContractResource extends Resource
{
    protected static ?string $model = Contract::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationGroup = 'CRM';

    protected static ?string $navigationLabel = '계약 관리';

    protected static ?string $modelLabel = '계약';

    protected static ?string $pluralModelLabel = '계약';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('계약 정보')
                    ->schema([
                        Forms\Components\TextInput::make('contract_number')
                            ->label('계약 번호')
                            ->disabled()
                            ->dehydrated(false)
                            ->placeholder('자동 생성'),

                        Forms\Components\TextInput::make('title')
                            ->label('계약명')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\Select::make('customer_id')
                            ->label('고객사')
                            ->relationship('customer', 'company_name')
                            ->searchable()
                            ->preload()
                            ->required(),

                        Forms\Components\Select::make('opportunity_id')
                            ->label('영업 기회')
                            ->relationship(
                                'opportunity',
                                'name',
                                fn ($query, $get) => $query->where('customer_id', $get('customer_id'))
                            )
                            ->searchable()
                            ->preload()
                            ->visible(fn ($get) => filled($get('customer_id'))),
                    ])->columns(2),

                Forms\Components\Section::make('계약 조건')
                    ->schema([
                        Forms\Components\TextInput::make('amount')
                            ->label('계약 금액')
                            ->numeric()
                            ->prefix('₩')
                            ->required(),

                        Forms\Components\Select::make('payment_terms')
                            ->label('결제 조건')
                            ->options([
                                '일시불' => '일시불',
                                '분할' => '분할',
                                '월정액' => '월정액',
                                '마일스톤' => '마일스톤 기반',
                            ])
                            ->default('일시불')
                            ->required(),

                        Forms\Components\DatePicker::make('start_date')
                            ->label('계약 시작일')
                            ->required(),

                        Forms\Components\DatePicker::make('end_date')
                            ->label('계약 종료일')
                            ->required()
                            ->after('start_date'),
                    ])->columns(2),

                Forms\Components\Section::make('계약 상태')
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->label('상태')
                            ->options([
                                '작성중' => '작성중',
                                '검토중' => '검토중',
                                '서명대기' => '서명대기',
                                '진행중' => '진행중',
                                '완료' => '완료',
                                '해지' => '해지',
                            ])
                            ->default('작성중')
                            ->required(),

                        Forms\Components\Select::make('signed_by')
                            ->label('서명자')
                            ->relationship('signer', 'name')
                            ->searchable()
                            ->preload(),

                        Forms\Components\DateTimePicker::make('signed_at')
                            ->label('서명일시'),

                        Forms\Components\FileUpload::make('file_path')
                            ->label('계약서 파일')
                            ->directory('contracts')
                            ->acceptedFileTypes(['application/pdf'])
                            ->maxSize(10240),
                    ])->columns(2),

                Forms\Components\Section::make('상세 내용')
                    ->schema([
                        Forms\Components\Textarea::make('description')
                            ->label('계약 내용')
                            ->rows(4)
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('계약 정보')
                    ->id('contract-info')
                    ->description(fn ($record) => $record->contract_number)
                    ->schema([
                        Infolists\Components\TextEntry::make('contract_number')
                            ->label('계약 번호'),

                        Infolists\Components\TextEntry::make('title')
                            ->label('계약명'),

                        Infolists\Components\TextEntry::make('customer.company_name')
                            ->label('고객사'),

                        Infolists\Components\TextEntry::make('opportunity.name')
                            ->label('영업 기회')
                            ->placeholder('-'),
                    ])
                    ->columns(2)
                    ->collapsible()
                    ->persistCollapsed(),

                Infolists\Components\Section::make('계약 조건')
                    ->id('contract-terms')
                    ->description(fn ($record) => number_format($record->amount) . '원')
                    ->schema([
                        Infolists\Components\TextEntry::make('amount')
                            ->label('계약 금액')
                            ->money('KRW'),

                        Infolists\Components\TextEntry::make('payment_terms')
                            ->label('결제 조건')
                            ->badge(),

                        Infolists\Components\TextEntry::make('start_date')
                            ->label('계약 시작일')
                            ->date('Y-m-d'),

                        Infolists\Components\TextEntry::make('end_date')
                            ->label('계약 종료일')
                            ->date('Y-m-d'),

                        Infolists\Components\TextEntry::make('duration_months')
                            ->label('계약 기간')
                            ->suffix('개월'),
                    ])
                    ->columns(2)
                    ->collapsible()
                    ->persistCollapsed(),

                Infolists\Components\Section::make('계약 상태')
                    ->id('contract-status')
                    ->description(fn ($record) => $record->status)
                    ->schema([
                        Infolists\Components\TextEntry::make('status')
                            ->label('상태')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                '작성중' => 'gray',
                                '검토중' => 'info',
                                '서명대기' => 'warning',
                                '진행중' => 'success',
                                '완료' => 'primary',
                                '해지' => 'danger',
                                default => 'gray',
                            }),

                        Infolists\Components\TextEntry::make('signer.name')
                            ->label('서명자')
                            ->placeholder('-'),

                        Infolists\Components\TextEntry::make('signed_at')
                            ->label('서명일시')
                            ->dateTime('Y-m-d H:i')
                            ->placeholder('-'),

                        Infolists\Components\TextEntry::make('is_expired')
                            ->label('만료 여부')
                            ->formatStateUsing(fn ($state) => $state ? '만료됨' : '유효')
                            ->badge()
                            ->color(fn ($state) => $state ? 'danger' : 'success'),

                        Infolists\Components\TextEntry::make('description')
                            ->label('계약 내용')
                            ->columnSpanFull(),

                        Infolists\Components\TextEntry::make('created_at')
                            ->label('등록일')
                            ->dateTime('Y-m-d H:i'),

                        Infolists\Components\TextEntry::make('updated_at')
                            ->label('수정일')
                            ->dateTime('Y-m-d H:i'),
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
                Tables\Columns\TextColumn::make('contract_number')
                    ->label('계약 번호')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('title')
                    ->label('계약명')
                    ->searchable()
                    ->sortable()
                    ->limit(30),

                Tables\Columns\TextColumn::make('customer.company_name')
                    ->label('고객사')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('amount')
                    ->label('계약 금액')
                    ->money('KRW')
                    ->sortable(),

                Tables\Columns\TextColumn::make('status')
                    ->label('상태')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        '작성중' => 'gray',
                        '검토중' => 'info',
                        '서명대기' => 'warning',
                        '진행중' => 'success',
                        '완료' => 'primary',
                        '해지' => 'danger',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('start_date')
                    ->label('시작일')
                    ->date('Y-m-d')
                    ->sortable(),

                Tables\Columns\TextColumn::make('end_date')
                    ->label('종료일')
                    ->date('Y-m-d')
                    ->sortable(),

                Tables\Columns\TextColumn::make('signer.name')
                    ->label('서명자')
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('등록일')
                    ->date('Y-m-d')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('상태')
                    ->options([
                        '작성중' => '작성중',
                        '검토중' => '검토중',
                        '서명대기' => '서명대기',
                        '진행중' => '진행중',
                        '완료' => '완료',
                        '해지' => '해지',
                    ]),

                Tables\Filters\SelectFilter::make('customer_id')
                    ->label('고객사')
                    ->relationship('customer', 'company_name')
                    ->searchable()
                    ->preload(),

                Tables\Filters\SelectFilter::make('payment_terms')
                    ->label('결제 조건')
                    ->options([
                        '일시불' => '일시불',
                        '분할' => '분할',
                        '월정액' => '월정액',
                        '마일스톤' => '마일스톤 기반',
                    ]),
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListContracts::route('/'),
            'create' => Pages\CreateContract::route('/create'),
            'view' => Pages\ViewContract::route('/{record}'),
            'edit' => Pages\EditContract::route('/{record}/edit'),
        ];
    }
}
