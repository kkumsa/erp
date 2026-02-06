<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OpportunityResource\Pages;
use App\Models\Opportunity;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class OpportunityResource extends Resource
{
    protected static ?string $model = Opportunity::class;

    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';

    protected static ?string $navigationGroup = 'CRM';

    protected static ?string $navigationLabel = '영업 기회';

    protected static ?string $modelLabel = '영업 기회';

    protected static ?string $pluralModelLabel = '영업 기회';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('기본 정보')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('기회명')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\Select::make('customer_id')
                            ->label('고객사')
                            ->relationship('customer', 'company_name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->live(),

                        Forms\Components\Select::make('contact_id')
                            ->label('연락처')
                            ->relationship(
                                'contact',
                                'name',
                                fn ($query, $get) => $query->where('customer_id', $get('customer_id'))
                            )
                            ->searchable()
                            ->preload()
                            ->visible(fn ($get) => filled($get('customer_id'))),

                        Forms\Components\Select::make('assigned_to')
                            ->label('영업 담당자')
                            ->relationship('assignedUser', 'name')
                            ->searchable()
                            ->preload(),
                    ])->columns(2),

                Forms\Components\Section::make('영업 정보')
                    ->schema([
                        Forms\Components\TextInput::make('amount')
                            ->label('예상 금액')
                            ->numeric()
                            ->prefix('₩')
                            ->default(0),

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
                            ->default(10)
                            ->minValue(0)
                            ->maxValue(100),

                        Forms\Components\DatePicker::make('expected_close_date')
                            ->label('예상 계약일'),

                        Forms\Components\DatePicker::make('actual_close_date')
                            ->label('실제 계약일')
                            ->visible(fn ($get) => in_array($get('stage'), ['계약완료', '실패'])),

                        Forms\Components\TextInput::make('next_step')
                            ->label('다음 단계')
                            ->maxLength(255),
                    ])->columns(2),

                Forms\Components\Section::make('상세 내용')
                    ->schema([
                        Forms\Components\Textarea::make('description')
                            ->label('설명')
                            ->rows(4)
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('기본 정보')
                    ->id('opportunity-info')
                    ->description(fn ($record) => $record->name)
                    ->schema([
                        Infolists\Components\TextEntry::make('name')
                            ->label('기회명'),

                        Infolists\Components\TextEntry::make('customer.company_name')
                            ->label('고객사'),

                        Infolists\Components\TextEntry::make('contact.name')
                            ->label('연락처'),

                        Infolists\Components\TextEntry::make('assignedUser.name')
                            ->label('영업 담당자'),
                    ])
                    ->columns(2)
                    ->collapsible()
                    ->persistCollapsed(),

                Infolists\Components\Section::make('영업 정보')
                    ->id('opportunity-sales')
                    ->description(fn ($record) => $record->stage)
                    ->schema([
                        Infolists\Components\TextEntry::make('amount')
                            ->label('예상 금액')
                            ->money('KRW'),

                        Infolists\Components\TextEntry::make('stage')
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

                        Infolists\Components\TextEntry::make('probability')
                            ->label('성공 확률')
                            ->suffix('%'),

                        Infolists\Components\TextEntry::make('weighted_amount')
                            ->label('가중 금액')
                            ->money('KRW'),

                        Infolists\Components\TextEntry::make('expected_close_date')
                            ->label('예상 계약일')
                            ->date('Y-m-d'),

                        Infolists\Components\TextEntry::make('actual_close_date')
                            ->label('실제 계약일')
                            ->date('Y-m-d'),

                        Infolists\Components\TextEntry::make('next_step')
                            ->label('다음 단계'),
                    ])
                    ->columns(2)
                    ->collapsible()
                    ->persistCollapsed(),

                Infolists\Components\Section::make('상세 내용')
                    ->id('opportunity-description')
                    ->description(fn ($record) => $record->description ? mb_substr($record->description, 0, 30) . '...' : '-')
                    ->schema([
                        Infolists\Components\TextEntry::make('description')
                            ->label('설명')
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
                Tables\Columns\TextColumn::make('name')
                    ->label('기회명')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('customer.company_name')
                    ->label('고객사')
                    ->searchable()
                    ->sortable(),

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
                    ->suffix('%')
                    ->sortable(),

                Tables\Columns\TextColumn::make('expected_close_date')
                    ->label('예상 계약일')
                    ->date('Y-m-d')
                    ->sortable(),

                Tables\Columns\TextColumn::make('assignedUser.name')
                    ->label('담당자'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('등록일')
                    ->date('Y-m-d')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('stage')
                    ->label('단계')
                    ->options([
                        '발굴' => '발굴',
                        '접촉' => '접촉',
                        '제안' => '제안',
                        '협상' => '협상',
                        '계약완료' => '계약완료',
                        '실패' => '실패',
                    ]),

                Tables\Filters\SelectFilter::make('customer_id')
                    ->label('고객사')
                    ->relationship('customer', 'company_name')
                    ->searchable()
                    ->preload(),

                Tables\Filters\SelectFilter::make('assigned_to')
                    ->label('담당자')
                    ->relationship('assignedUser', 'name'),
            ])
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
            'index' => Pages\ListOpportunities::route('/'),
            'create' => Pages\CreateOpportunity::route('/create'),
            'view' => Pages\ViewOpportunity::route('/{record}'),
            'edit' => Pages\EditOpportunity::route('/{record}/edit'),
        ];
    }
}
