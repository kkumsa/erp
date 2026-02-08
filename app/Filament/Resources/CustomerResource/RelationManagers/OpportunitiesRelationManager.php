<?php

namespace App\Filament\Resources\CustomerResource\RelationManagers;

use App\Enums\OpportunityStage;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class OpportunitiesRelationManager extends RelationManager
{
    protected static string $relationship = 'opportunities';

    protected static ?string $title = null;

    protected static ?string $modelLabel = null;

    public static function getTitle(\Illuminate\Database\Eloquent\Model $ownerRecord, string $pageClass): string
    {
        return __('models.opportunity');
    }

    public static function getModelLabel(): string
    {
        return __('models.opportunity');
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label(__('fields.name'))
                    ->required()
                    ->maxLength(255),

                Forms\Components\Select::make('contact_id')
                    ->label(__('fields.contact_id'))
                    ->relationship('contact', 'name')
                    ->searchable()
                    ->preload(),

                Forms\Components\TextInput::make('amount')
                    ->label(__('fields.amount'))
                    ->numeric()
                    ->prefix('₩'),

                Forms\Components\Select::make('stage')
                    ->label(__('fields.stage'))
                    ->options(OpportunityStage::class)
                    ->default(OpportunityStage::Discovery)
                    ->required(),

                Forms\Components\TextInput::make('probability')
                    ->label(__('fields.probability'))
                    ->numeric()
                    ->suffix('%')
                    ->default(10),

                Forms\Components\DatePicker::make('expected_close_date')
                    ->label(__('fields.expected_close_date')),

                Forms\Components\Select::make('assigned_to')
                    ->label(__('fields.assigned_to'))
                    ->relationship('assignedUser', 'name')
                    ->searchable()
                    ->preload(),

                Forms\Components\Textarea::make('description')
                    ->label(__('fields.description'))
                    ->rows(2)
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label(__('fields.name'))
                    ->searchable(),

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
                    ->suffix('%'),

                Tables\Columns\TextColumn::make('expected_close_date')
                    ->label(__('fields.expected_close_date'))
                    ->date('Y.m.d'),

                Tables\Columns\TextColumn::make('assignedUser.name')
                    ->label(__('fields.assigned_to')),
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
