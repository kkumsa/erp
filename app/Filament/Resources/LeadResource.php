<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LeadResource\Pages;
use App\Models\Lead;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use App\Filament\Traits\HasResourcePermissions;

class LeadResource extends Resource
{
    use HasResourcePermissions;

    protected static ?string $permissionPrefix = 'lead';

    protected static ?string $model = Lead::class;

    protected static ?string $navigationIcon = 'heroicon-o-funnel';

    protected static ?string $navigationGroup = 'CRM';

    protected static ?string $navigationLabel = '잠재 고객 발굴';

    protected static ?string $modelLabel = '잠재 고객';

    protected static ?string $pluralModelLabel = '잠재 고객';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('기본 정보')
                    ->schema([
                        Forms\Components\TextInput::make('company_name')
                            ->label('회사명')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\TextInput::make('contact_name')
                            ->label('담당자')
                            ->maxLength(255),

                        Forms\Components\TextInput::make('email')
                            ->label('이메일')
                            ->email()
                            ->maxLength(255),

                        Forms\Components\TextInput::make('phone')
                            ->label('전화번호')
                            ->tel()
                            ->maxLength(20),

                        Forms\Components\Select::make('source')
                            ->label('유입 경로')
                            ->options([
                                '웹사이트' => '웹사이트',
                                '소개' => '소개',
                                '광고' => '광고',
                                '전시회' => '전시회',
                                '기타' => '기타',
                            ]),

                        Forms\Components\Select::make('status')
                            ->label('상태')
                            ->options([
                                '신규' => '신규',
                                '연락중' => '연락중',
                                '적격' => '적격',
                                '부적격' => '부적격',
                                '전환' => '전환',
                            ])
                            ->default('신규')
                            ->required(),
                    ])->columns(2),

                Forms\Components\Section::make('추가 정보')
                    ->schema([
                        Forms\Components\TextInput::make('expected_revenue')
                            ->label('예상 매출')
                            ->numeric()
                            ->prefix('₩'),

                        Forms\Components\Select::make('assigned_to')
                            ->label('담당자')
                            ->relationship('assignedUser', 'name')
                            ->searchable()
                            ->preload(),

                        Forms\Components\Textarea::make('description')
                            ->label('설명')
                            ->rows(4)
                            ->columnSpanFull(),
                    ])->columns(2),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('기본 정보')
                    ->id('lead-info')
                    ->description(fn ($record) => $record->company_name)
                    ->schema([
                        Infolists\Components\TextEntry::make('company_name')
                            ->label('회사명'),

                        Infolists\Components\TextEntry::make('contact_name')
                            ->label('담당자'),

                        Infolists\Components\TextEntry::make('email')
                            ->label('이메일'),

                        Infolists\Components\TextEntry::make('phone')
                            ->label('전화번호'),

                        Infolists\Components\TextEntry::make('source')
                            ->label('유입 경로')
                            ->badge(),

                        Infolists\Components\TextEntry::make('status')
                            ->label('상태')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                '신규' => 'info',
                                '연락중' => 'warning',
                                '적격' => 'success',
                                '부적격' => 'gray',
                                '전환' => 'primary',
                                default => 'gray',
                            }),
                    ])
                    ->columns(2)
                    ->collapsible()
                    ->persistCollapsed(),

                Infolists\Components\Section::make('추가 정보')
                    ->id('lead-extra')
                    ->description(fn ($record) => $record->status)
                    ->schema([
                        Infolists\Components\TextEntry::make('expected_revenue')
                            ->label('예상 매출')
                            ->money('KRW'),

                        Infolists\Components\TextEntry::make('assignedUser.name')
                            ->label('담당자'),

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
                Tables\Columns\TextColumn::make('company_name')
                    ->label('회사명')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('contact_name')
                    ->label('담당자'),

                Tables\Columns\TextColumn::make('email')
                    ->label('이메일'),

                Tables\Columns\TextColumn::make('source')
                    ->label('유입 경로')
                    ->badge(),

                Tables\Columns\TextColumn::make('status')
                    ->label('상태')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        '신규' => 'info',
                        '연락중' => 'warning',
                        '적격' => 'success',
                        '부적격' => 'gray',
                        '전환' => 'primary',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('expected_revenue')
                    ->label('예상 매출')
                    ->money('KRW')
                    ->sortable(),

                Tables\Columns\TextColumn::make('assignedUser.name')
                    ->label('담당자'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('등록일')
                    ->date('Y-m-d')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('상태')
                    ->options([
                        '신규' => '신규',
                        '연락중' => '연락중',
                        '적격' => '적격',
                        '부적격' => '부적격',
                        '전환' => '전환',
                    ]),

                Tables\Filters\SelectFilter::make('source')
                    ->label('유입 경로')
                    ->options([
                        '웹사이트' => '웹사이트',
                        '소개' => '소개',
                        '광고' => '광고',
                        '전시회' => '전시회',
                        '기타' => '기타',
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
            'index' => Pages\ListLeads::route('/'),
            'create' => Pages\CreateLead::route('/create'),
            'view' => Pages\ViewLead::route('/{record}'),
            'edit' => Pages\EditLead::route('/{record}/edit'),
        ];
    }
}
