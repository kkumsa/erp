<?php

namespace App\Filament\Resources;

use App\Enums\LeadSource;
use App\Enums\LeadStatus;
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

    protected static ?int $navigationSort = 1;

    public static function getNavigationGroup(): ?string
    {
        return __('navigation.groups.crm');
    }

    public static function getNavigationLabel(): string
    {
        return __('navigation.labels.lead');
    }

    public static function getModelLabel(): string
    {
        return __('models.lead');
    }

    public static function getPluralModelLabel(): string
    {
        return __('models.lead_plural');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(__('common.sections.basic_info'))
                    ->schema([
                        Forms\Components\TextInput::make('company_name')
                            ->label(__('fields.company_name'))
                            ->required()
                            ->maxLength(255),

                        Forms\Components\TextInput::make('contact_name')
                            ->label(__('fields.contact_name'))
                            ->maxLength(255),

                        Forms\Components\TextInput::make('email')
                            ->label(__('fields.email'))
                            ->email()
                            ->maxLength(255),

                        Forms\Components\TextInput::make('phone')
                            ->label(__('fields.phone'))
                            ->tel()
                            ->maxLength(20),

                        Forms\Components\Select::make('source')
                            ->label(__('fields.source'))
                            ->options(LeadSource::class),

                        Forms\Components\Select::make('status')
                            ->label(__('fields.status'))
                            ->options(LeadStatus::class)
                            ->default(LeadStatus::New)
                            ->required(),
                    ])->columns(2),

                Forms\Components\Section::make(__('common.sections.additional_info'))
                    ->schema([
                        Forms\Components\TextInput::make('expected_revenue')
                            ->label(__('fields.expected_revenue'))
                            ->numeric()
                            ->prefix('₩'),

                        Forms\Components\Select::make('assigned_to')
                            ->label(__('fields.assigned_to'))
                            ->relationship('assignedUser', 'name')
                            ->searchable()
                            ->preload(),

                        Forms\Components\Textarea::make('description')
                            ->label(__('fields.description'))
                            ->rows(4)
                            ->columnSpanFull(),
                    ])->columns(2),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make(__('common.sections.basic_info'))
                    ->id('lead-info')
                    ->description(fn ($record) => $record->company_name)
                    ->schema([
                        Infolists\Components\TextEntry::make('company_name')
                            ->label(__('fields.company_name')),

                        Infolists\Components\TextEntry::make('contact_name')
                            ->label(__('fields.contact_name')),

                        Infolists\Components\TextEntry::make('email')
                            ->label(__('fields.email')),

                        Infolists\Components\TextEntry::make('phone')
                            ->label(__('fields.phone')),

                        Infolists\Components\TextEntry::make('source')
                            ->label(__('fields.source'))
                            ->badge(),

                        Infolists\Components\TextEntry::make('status')
                            ->label(__('fields.status'))
                            ->badge()
                            ->color(fn ($state) => $state instanceof LeadStatus ? $state->color() : (LeadStatus::tryFrom($state)?->color() ?? 'gray')),
                    ])
                    ->columns(2)
                    ->collapsible()
                    ->persistCollapsed(),

                Infolists\Components\Section::make(__('common.sections.additional_info'))
                    ->id('lead-extra')
                    ->description(fn ($record) => $record->status instanceof LeadStatus ? $record->status->getLabel() : ($record->status ?? null))
                    ->schema([
                        Infolists\Components\TextEntry::make('expected_revenue')
                            ->label(__('fields.expected_revenue'))
                            ->money('KRW'),

                        Infolists\Components\TextEntry::make('assignedUser.name')
                            ->label(__('fields.assigned_to')),

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
                Tables\Columns\TextColumn::make('company_name')
                    ->label(__('fields.company_name'))
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('contact_name')
                    ->label(__('fields.contact_name')),

                Tables\Columns\TextColumn::make('email')
                    ->label(__('fields.email')),

                Tables\Columns\TextColumn::make('source')
                    ->label(__('fields.source'))
                    ->badge(),

                Tables\Columns\TextColumn::make('status')
                    ->label(__('fields.status'))
                    ->badge()
                    ->color(fn ($state) => $state instanceof LeadStatus ? $state->color() : (LeadStatus::tryFrom($state)?->color() ?? 'gray')),

                Tables\Columns\TextColumn::make('expected_revenue')
                    ->label(__('fields.expected_revenue'))
                    ->money('KRW')
                    ->sortable(),

                Tables\Columns\TextColumn::make('assignedUser.name')
                    ->label(__('fields.assigned_to')),

                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('fields.created_at'))
                    ->date('Y.m.d')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label(__('fields.status'))
                    ->options(LeadStatus::class),

                Tables\Filters\SelectFilter::make('source')
                    ->label(__('fields.source'))
                    ->options(LeadSource::class),
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
