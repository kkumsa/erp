<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CustomerResource\Pages;
use App\Filament\Resources\CustomerResource\RelationManagers;
use App\Models\Customer;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class CustomerResource extends Resource
{
    protected static ?string $model = Customer::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office';

    protected static ?string $navigationGroup = 'CRM';

    protected static ?string $navigationLabel = '고객 관리';

    protected static ?string $modelLabel = '고객';

    protected static ?string $pluralModelLabel = '고객';

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

                        Forms\Components\TextInput::make('business_number')
                            ->label('사업자번호')
                            ->unique(ignoreRecord: true)
                            ->mask('999-99-99999')
                            ->maxLength(12),

                        Forms\Components\TextInput::make('representative')
                            ->label('대표자')
                            ->maxLength(100),

                        Forms\Components\TextInput::make('industry')
                            ->label('업종')
                            ->maxLength(100),

                        Forms\Components\TextInput::make('business_type')
                            ->label('업태')
                            ->maxLength(100),

                        Forms\Components\Select::make('assigned_to')
                            ->label('담당자')
                            ->relationship('assignedUser', 'name')
                            ->searchable()
                            ->preload(),
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

                        Forms\Components\TextInput::make('website')
                            ->label('웹사이트')
                            ->url()
                            ->maxLength(255),

                        Forms\Components\Textarea::make('address')
                            ->label('주소')
                            ->rows(2)
                            ->columnSpanFull(),
                    ])->columns(2),

                Forms\Components\Section::make('분류')
                    ->schema([
                        Forms\Components\Select::make('type')
                            ->label('고객 유형')
                            ->options([
                                '잠재고객' => '잠재고객',
                                '고객' => '고객',
                                'VIP' => 'VIP',
                                '휴면' => '휴면',
                            ])
                            ->default('잠재고객')
                            ->required(),

                        Forms\Components\Select::make('status')
                            ->label('상태')
                            ->options([
                                '활성' => '활성',
                                '비활성' => '비활성',
                            ])
                            ->default('활성')
                            ->required(),

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
                Infolists\Components\Section::make('기업 정보')
                    ->id('customer-info')
                    ->description(fn ($record) => $record->company_name)
                    ->schema([
                        Infolists\Components\TextEntry::make('company_name')
                            ->label('회사명'),

                        Infolists\Components\TextEntry::make('business_number')
                            ->label('사업자번호'),

                        Infolists\Components\TextEntry::make('representative')
                            ->label('대표자'),

                        Infolists\Components\TextEntry::make('industry')
                            ->label('업종'),

                        Infolists\Components\TextEntry::make('business_type')
                            ->label('업태'),

                        Infolists\Components\TextEntry::make('assignedUser.name')
                            ->label('담당자'),
                    ])
                    ->columns(2)
                    ->collapsible()
                    ->persistCollapsed(),

                Infolists\Components\Section::make('연락처')
                    ->id('customer-contact')
                    ->description(fn ($record) => $record->phone ?? '-')
                    ->schema([
                        Infolists\Components\TextEntry::make('phone')
                            ->label('전화번호'),

                        Infolists\Components\TextEntry::make('fax')
                            ->label('팩스'),

                        Infolists\Components\TextEntry::make('email')
                            ->label('이메일'),

                        Infolists\Components\TextEntry::make('website')
                            ->label('웹사이트')
                            ->url(fn ($record) => $record->website),

                        Infolists\Components\TextEntry::make('address')
                            ->label('주소')
                            ->columnSpanFull(),
                    ])
                    ->columns(2)
                    ->collapsible()
                    ->persistCollapsed(),

                Infolists\Components\Section::make('분류')
                    ->id('customer-category')
                    ->description(fn ($record) => $record->type)
                    ->schema([
                        Infolists\Components\TextEntry::make('type')
                            ->label('고객 유형')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                '잠재고객' => 'gray',
                                '고객' => 'success',
                                'VIP' => 'warning',
                                '휴면' => 'danger',
                                default => 'gray',
                            }),

                        Infolists\Components\TextEntry::make('status')
                            ->label('상태')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                '활성' => 'success',
                                '비활성' => 'gray',
                                default => 'gray',
                            }),

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
                Tables\Columns\TextColumn::make('company_name')
                    ->label('회사명')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('business_number')
                    ->label('사업자번호')
                    ->searchable(),

                Tables\Columns\TextColumn::make('representative')
                    ->label('대표자'),

                Tables\Columns\TextColumn::make('phone')
                    ->label('전화번호'),

                Tables\Columns\TextColumn::make('type')
                    ->label('유형')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        '잠재고객' => 'gray',
                        '고객' => 'success',
                        'VIP' => 'warning',
                        '휴면' => 'danger',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('assignedUser.name')
                    ->label('담당자'),

                Tables\Columns\TextColumn::make('status')
                    ->label('상태')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        '활성' => 'success',
                        '비활성' => 'gray',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('등록일')
                    ->date('Y-m-d')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->label('유형')
                    ->options([
                        '잠재고객' => '잠재고객',
                        '고객' => '고객',
                        'VIP' => 'VIP',
                        '휴면' => '휴면',
                    ]),

                Tables\Filters\SelectFilter::make('status')
                    ->label('상태')
                    ->options([
                        '활성' => '활성',
                        '비활성' => '비활성',
                    ]),

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
            RelationManagers\ContactsRelationManager::class,
            RelationManagers\OpportunitiesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCustomers::route('/'),
            'create' => Pages\CreateCustomer::route('/create'),
            'view' => Pages\ViewCustomer::route('/{record}'),
            'edit' => Pages\EditCustomer::route('/{record}/edit'),
        ];
    }
}
