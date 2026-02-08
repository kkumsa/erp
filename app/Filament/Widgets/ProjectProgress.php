<?php

namespace App\Filament\Widgets;

use App\Enums\Priority;
use App\Enums\ProjectStatus;
use App\Filament\Resources\ProjectResource;
use App\Models\Project;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class ProjectProgress extends BaseWidget
{
    protected static ?int $sort = -2;

    protected int | string | array $columnSpan = 3;

    public function getTableHeading(): string|\Illuminate\Contracts\Support\Htmlable|null
    {
        return __('common.widgets.active_projects');
    }

    public static function canView(): bool
    {
        $user = auth()->user();

        return $user && $user->can('project.view');
    }

    public function table(Table $table): Table
    {
        $user = auth()->user();

        // Employee: 본인이 참여한 프로젝트만
        $query = Project::query()
            ->where('status', ProjectStatus::InProgress->value)
            ->orderBy('end_date')
            ->limit(5);

        if ($user && !$user->hasAnyRole(['Super Admin', 'Admin', 'Manager'])) {
            $query->where(function ($q) use ($user) {
                $q->where('manager_id', $user->id)
                  ->orWhereHas('members', fn ($mq) => $mq->where('users.id', $user->id));
            });
        }

        return $table
            ->query($query)
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label(__('fields.project'))
                    ->limit(25),

                Tables\Columns\TextColumn::make('customer.company_name')
                    ->label(__('fields.customer'))
                    ->limit(15),

                Tables\Columns\TextColumn::make('manager.name')
                    ->label('PM'),

                Tables\Columns\TextColumn::make('end_date')
                    ->label(__('fields.deadline'))
                    ->date('Y.m.d')
                    ->color(fn ($record) => $record->is_delayed ? 'danger' : null),

                Tables\Columns\TextColumn::make('progress')
                    ->label(__('fields.progress'))
                    ->suffix('%')
                    ->color(fn ($state) => $state >= 80 ? 'success' : ($state >= 50 ? 'warning' : 'info')),

                Tables\Columns\TextColumn::make('priority')
                    ->label(__('fields.priority'))
                    ->badge()
                    ->formatStateUsing(fn ($state) => $state instanceof Priority ? $state->getLabel() : (Priority::tryFrom($state)?->getLabel() ?? $state))
                    ->color(fn ($state) => $state instanceof Priority ? $state->color() : (Priority::tryFrom($state)?->color() ?? 'gray')),
            ])
            ->actions([
                Tables\Actions\Action::make('view')
                    ->label(__('common.buttons.view'))
                    ->url(fn (Project $record): string => ProjectResource::getUrl('view', ['record' => $record])),
            ])
            ->paginated(false);
    }
}
