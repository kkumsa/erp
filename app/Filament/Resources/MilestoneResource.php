<?php

namespace App\Filament\Resources;

use App\Enums\MilestoneStatus;
use App\Filament\Resources\MilestoneResource\Pages;
use App\Models\Milestone;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use App\Filament\Traits\HasResourcePermissions;

class MilestoneResource extends Resource
{
    use HasResourcePermissions;

    protected static ?string $permissionPrefix = 'project';

    protected static ?string $model = Milestone::class;

    protected static ?string $navigationIcon = 'heroicon-o-flag';

    protected static ?int $navigationSort = 4;

    public static function getNavigationGroup(): ?string
    {
        return __('navigation.groups.project');
    }

    public static function getNavigationLabel(): string
    {
        return __('navigation.labels.milestone');
    }

    public static function getModelLabel(): string
    {
        return __('models.milestone');
    }

    public static function getPluralModelLabel(): string
    {
        return __('models.milestone_plural');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('project_id')
                    ->label(__('fields.project'))
                    ->relationship('project', 'name')
                    ->required()
                    ->searchable()
                    ->preload(),

                Forms\Components\TextInput::make('name')
                    ->label(__('fields.name'))
                    ->required()
                    ->maxLength(255),

                Forms\Components\Textarea::make('description')
                    ->label(__('fields.description'))
                    ->rows(4)
                    ->columnSpanFull(),

                Forms\Components\DatePicker::make('due_date')
                    ->label(__('fields.deadline')),

                Forms\Components\Select::make('status')
                    ->label(__('fields.status'))
                    ->options(MilestoneStatus::class)
                    ->default(MilestoneStatus::Pending),

                Forms\Components\TextInput::make('sort_order')
                    ->label(__('fields.sort_order'))
                    ->numeric(),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make(__('common.sections.milestone_info'))
                    ->id('milestone-info')
                    ->description(fn ($record) => $record->name)
                    ->schema([
                        Infolists\Components\TextEntry::make('name')
                            ->label(__('fields.name')),

                        Infolists\Components\TextEntry::make('project.name')
                            ->label(__('fields.project')),

                        Infolists\Components\TextEntry::make('status')
                            ->label(__('fields.status'))
                            ->badge()
                            ->color(fn ($state) => $state instanceof MilestoneStatus ? $state->color() : (MilestoneStatus::tryFrom($state)?->color() ?? 'gray')),

                        Infolists\Components\TextEntry::make('due_date')
                            ->label(__('fields.deadline'))
                            ->date('Y.m.d'),

                        Infolists\Components\TextEntry::make('completed_date')
                            ->label(__('fields.completed_date'))
                            ->date('Y.m.d'),

                        Infolists\Components\TextEntry::make('description')
                            ->label(__('fields.description'))
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
                Tables\Columns\TextColumn::make('name')
                    ->label(__('fields.name'))
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('project.name')
                    ->label(__('fields.project'))
                    ->sortable(),

                Tables\Columns\TextColumn::make('status')
                    ->label(__('fields.status'))
                    ->badge()
                    ->color(fn ($state) => $state instanceof MilestoneStatus ? $state->color() : (MilestoneStatus::tryFrom($state)?->color() ?? 'gray')),

                Tables\Columns\TextColumn::make('due_date')
                    ->label(__('fields.deadline'))
                    ->date('Y.m.d')
                    ->sortable(),

                Tables\Columns\TextColumn::make('tasks_count')
                    ->label(__('fields.task'))
                    ->counts('tasks')
                    ->sortable(),
            ])
            ->defaultSort('sort_order', 'asc')
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
            'index' => Pages\ListMilestones::route('/'),
            'create' => Pages\CreateMilestone::route('/create'),
            'edit' => Pages\EditMilestone::route('/{record}/edit'),
        ];
    }
}
