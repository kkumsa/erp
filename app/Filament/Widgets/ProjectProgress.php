<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\ProjectResource;
use App\Models\Project;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class ProjectProgress extends BaseWidget
{
    protected static ?string $heading = '진행 중인 프로젝트';

    protected static ?int $sort = 4;

    protected int | string | array $columnSpan = 3;

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
            ->where('status', '진행중')
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
                    ->label('프로젝트')
                    ->limit(25),

                Tables\Columns\TextColumn::make('customer.company_name')
                    ->label('고객')
                    ->limit(15),

                Tables\Columns\TextColumn::make('manager.name')
                    ->label('PM'),

                Tables\Columns\TextColumn::make('end_date')
                    ->label('마감일')
                    ->date('Y-m-d')
                    ->color(fn ($record) => $record->is_delayed ? 'danger' : null),

                Tables\Columns\TextColumn::make('progress')
                    ->label('진행률')
                    ->suffix('%')
                    ->color(fn ($state) => $state >= 80 ? 'success' : ($state >= 50 ? 'warning' : 'info')),

                Tables\Columns\TextColumn::make('priority')
                    ->label('우선순위')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        '낮음' => 'gray',
                        '보통' => 'info',
                        '높음' => 'warning',
                        '긴급' => 'danger',
                        default => 'gray',
                    }),
            ])
            ->actions([
                Tables\Actions\Action::make('view')
                    ->label('보기')
                    ->url(fn (Project $record): string => ProjectResource::getUrl('view', ['record' => $record])),
            ])
            ->paginated(false);
    }
}
