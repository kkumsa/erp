<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AccountResource\Pages;
use App\Models\Account;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use App\Filament\Traits\HasResourcePermissions;

class AccountResource extends Resource
{
    use HasResourcePermissions;

    protected static ?string $permissionPrefix = 'account';

    protected static ?string $model = Account::class;

    protected static ?string $navigationIcon = 'heroicon-o-calculator';

    protected static ?string $navigationGroup = '재무/회계';

    protected static ?string $navigationLabel = '계정과목';

    protected static ?string $modelLabel = '계정과목';

    protected static ?string $pluralModelLabel = '계정과목';

    protected static ?int $navigationSort = 5;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('계정과목 정보')
                    ->schema([
                        Forms\Components\TextInput::make('code')
                            ->label('코드')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(50),

                        Forms\Components\TextInput::make('name')
                            ->label('계정명')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\Select::make('type')
                            ->label('유형')
                            ->options([
                                '자산' => '자산',
                                '부채' => '부채',
                                '자본' => '자본',
                                '수익' => '수익',
                                '비용' => '비용',
                            ])
                            ->required(),

                        Forms\Components\Select::make('parent_id')
                            ->label('상위 계정')
                            ->relationship('parent', 'name')
                            ->searchable()
                            ->preload()
                            ->placeholder('없음'),

                        Forms\Components\Textarea::make('description')
                            ->label('설명')
                            ->rows(3)
                            ->columnSpanFull(),

                        Forms\Components\Toggle::make('is_active')
                            ->label('활성화')
                            ->default(true),

                        Forms\Components\TextInput::make('sort_order')
                            ->label('정렬')
                            ->numeric()
                            ->default(0),
                    ])->columns(2),
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

                Tables\Columns\TextColumn::make('name')
                    ->label('계정명')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('type')
                    ->label('유형')
                    ->badge(),

                Tables\Columns\TextColumn::make('parent.name')
                    ->label('상위 계정')
                    ->placeholder('-'),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('활성화')
                    ->boolean(),
            ])
            ->defaultSort('sort_order')
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->label('유형')
                    ->options([
                        '자산' => '자산',
                        '부채' => '부채',
                        '자본' => '자본',
                        '수익' => '수익',
                        '비용' => '비용',
                    ]),

                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('활성화'),
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

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('계정과목 정보')
                    ->schema([
                        Infolists\Components\TextEntry::make('code')->label('코드'),
                        Infolists\Components\TextEntry::make('name')->label('계정명'),
                        Infolists\Components\TextEntry::make('type')->label('유형')->badge(),
                        Infolists\Components\TextEntry::make('parent.name')->label('상위 계정')->placeholder('-'),
                        Infolists\Components\IconEntry::make('is_active')->label('활성화')->boolean(),
                        Infolists\Components\TextEntry::make('sort_order')->label('정렬'),
                        Infolists\Components\TextEntry::make('description')->label('설명')->columnSpanFull(),
                    ])->columns(2),
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
            'index' => Pages\ListAccounts::route('/'),
            'create' => Pages\CreateAccount::route('/create'),
            'edit' => Pages\EditAccount::route('/{record}/edit'),
        ];
    }
}
