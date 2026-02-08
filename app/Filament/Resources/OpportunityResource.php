<?php

namespace App\Filament\Resources;

use App\Enums\OpportunityStage;
use App\Filament\Resources\OpportunityResource\Pages;
use App\Models\Opportunity;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use App\Filament\Traits\HasResourcePermissions;

class OpportunityResource extends Resource
{
    use HasResourcePermissions;

    protected static ?string $permissionPrefix = 'opportunity';

    protected static ?string $model = Opportunity::class;

    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';

    protected static ?int $navigationSort = 2;

    public static function getNavigationGroup(): ?string
    {
        return __('navigation.groups.crm');
    }

    public static function getNavigationLabel(): string
    {
        return __('navigation.labels.opportunity');
    }

    public static function getModelLabel(): string
    {
        return __('models.opportunity');
    }

    public static function getPluralModelLabel(): string
    {
        return __('models.opportunity_plural');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(__('common.sections.basic_info'))
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label(__('fields.name'))
                            ->required()
                            ->maxLength(255),

                        Forms\Components\Select::make('customer_id')
                            ->label(__('fields.customer_id'))
                            ->relationship('customer', 'company_name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->live(),

                        Forms\Components\Select::make('contact_id')
                            ->label(__('fields.contact_id'))
                            ->relationship(
                                'contact',
                                'name',
                                fn ($query, $get) => $query->where('customer_id', $get('customer_id'))
                            )
                            ->searchable()
                            ->preload()
                            ->visible(fn ($get) => filled($get('customer_id'))),

                        Forms\Components\Select::make('assigned_to')
                            ->label(__('fields.assigned_to'))
                            ->relationship('assignedUser', 'name')
                            ->searchable()
                            ->preload(),
                    ])->columns(2),

                Forms\Components\Section::make(__('common.sections.sales_info'))
                    ->schema([
                        Forms\Components\TextInput::make('amount')
                            ->label(__('fields.amount'))
                            ->numeric()
                            ->prefix('₩')
                            ->default(0),

                        Forms\Components\Select::make('stage')
                            ->label(__('fields.stage'))
                            ->options(OpportunityStage::class)
                            ->default(OpportunityStage::Discovery)
                            ->required(),

                        Forms\Components\TextInput::make('probability')
                            ->label(__('fields.probability'))
                            ->numeric()
                            ->suffix('%')
                            ->default(10)
                            ->minValue(0)
                            ->maxValue(100),

                        Forms\Components\DatePicker::make('expected_close_date')
                            ->label(__('fields.expected_close_date')),

                        Forms\Components\DatePicker::make('actual_close_date')
                            ->label(__('fields.actual_close_date'))
                            ->visible(fn ($get) => in_array($get('stage'), [OpportunityStage::ClosedWon->value, OpportunityStage::ClosedLost->value])),

                        Forms\Components\TextInput::make('next_step')
                            ->label(__('fields.next_step'))
                            ->maxLength(255),
                    ])->columns(2),

                Forms\Components\Section::make(__('common.sections.detail_content'))
                    ->schema([
                        Forms\Components\Textarea::make('description')
                            ->label(__('fields.description'))
                            ->rows(4)
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make(__('common.sections.basic_info'))
                    ->id('opportunity-info')
                    ->description(fn ($record) => $record->name)
                    ->schema([
                        Infolists\Components\TextEntry::make('name')
                            ->label(__('fields.name')),

                        Infolists\Components\TextEntry::make('customer.company_name')
                            ->label(__('fields.customer_id')),

                        Infolists\Components\TextEntry::make('contact.name')
                            ->label(__('fields.contact_id')),

                        Infolists\Components\TextEntry::make('assignedUser.name')
                            ->label(__('fields.assigned_to')),
                    ])
                    ->columns(2)
                    ->collapsible()
                    ->persistCollapsed(),

                Infolists\Components\Section::make(__('common.sections.sales_info'))
                    ->id('opportunity-sales')
                    ->description(fn ($record) => $record->stage instanceof OpportunityStage ? $record->stage->getLabel() : ($record->stage ?? null))
                    ->schema([
                        Infolists\Components\TextEntry::make('amount')
                            ->label(__('fields.amount'))
                            ->money('KRW'),

                        Infolists\Components\TextEntry::make('stage')
                            ->label(__('fields.stage'))
                            ->badge()
                            ->color(fn ($state) => $state instanceof OpportunityStage ? $state->color() : (OpportunityStage::tryFrom($state)?->color() ?? 'gray')),

                        Infolists\Components\TextEntry::make('probability')
                            ->label(__('fields.probability'))
                            ->suffix('%'),

                        Infolists\Components\TextEntry::make('weighted_amount')
                            ->label(__('fields.weighted_amount'))
                            ->money('KRW'),

                        Infolists\Components\TextEntry::make('expected_close_date')
                            ->label(__('fields.expected_close_date'))
                            ->date('Y.m.d'),

                        Infolists\Components\TextEntry::make('actual_close_date')
                            ->label(__('fields.actual_close_date'))
                            ->date('Y.m.d'),

                        Infolists\Components\TextEntry::make('next_step')
                            ->label(__('fields.next_step')),
                    ])
                    ->columns(2)
                    ->collapsible()
                    ->persistCollapsed(),

                Infolists\Components\Section::make(__('common.sections.detail_content'))
                    ->id('opportunity-description')
                    ->description(fn ($record) => $record->description ? mb_substr($record->description, 0, 30) . '...' : '-')
                    ->schema([
                        Infolists\Components\TextEntry::make('description')
                            ->label(__('fields.description'))
                            ->columnSpanFull(),

                        Infolists\Components\TextEntry::make('created_at')
                            ->label(__('fields.created_at'))
                            ->dateTime('Y.m.d H:i'),

                        Infolists\Components\TextEntry::make('updated_at')
                            ->label(__('fields.updated_at'))
                            ->dateTime('Y.m.d H:i'),
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
                    ->label(__('fields.name'))
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('customer.company_name')
                    ->label(__('fields.customer_id'))
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('amount')
                    ->label(__('fields.amount'))
                    ->money('KRW')
                    ->sortable(),

                Tables\Columns\TextColumn::make('stage')
                    ->label(__('fields.stage'))
                    ->badge()
                    ->color(fn ($state) => $state instanceof OpportunityStage ? $state->color() : (OpportunityStage::tryFrom($state)?->color() ?? 'gray')),

                Tables\Columns\TextColumn::make('probability')
                    ->label(__('fields.probability'))
                    ->suffix('%')
                    ->sortable(),

                Tables\Columns\TextColumn::make('expected_close_date')
                    ->label(__('fields.expected_close_date'))
                    ->date('Y.m.d')
                    ->sortable(),

                Tables\Columns\TextColumn::make('assignedUser.name')
                    ->label(__('fields.assigned_to')),

                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('fields.created_at'))
                    ->date('Y.m.d')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('stage')
                    ->label(__('fields.stage'))
                    ->options(OpportunityStage::class),

                Tables\Filters\SelectFilter::make('customer_id')
                    ->label(__('fields.customer_id'))
                    ->relationship('customer', 'company_name')
                    ->searchable()
                    ->preload(),

                Tables\Filters\SelectFilter::make('assigned_to')
                    ->label(__('fields.assigned_to'))
                    ->relationship('assignedUser', 'name'),
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
            'index' => Pages\ListOpportunities::route('/'),
            'create' => Pages\CreateOpportunity::route('/create'),
            'view' => Pages\ViewOpportunity::route('/{record}'),
            'edit' => Pages\EditOpportunity::route('/{record}/edit'),
        ];
    }
}
