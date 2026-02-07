<?php

namespace App\Filament\Pages;

use App\Models\DatabaseNotification;
use Filament\Pages\Page;
use Filament\Tables;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class NotificationHistory extends Page implements HasTable
{
    use InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-inbox-stack';
    protected static ?string $navigationGroup = '내 설정';
    protected static ?string $navigationLabel = '알림 내역';
    protected static ?string $title = '알림 내역';
    protected static ?int $navigationSort = 2;
    protected static string $view = 'filament.pages.notification-history';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                DatabaseNotification::query()
                    ->withTrashed()
                    ->where('notifiable_type', \App\Models\User::class)
                    ->where('notifiable_id', auth()->id())
            )
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('data.title')
                    ->label('제목')
                    ->searchable(query: function (Builder $query, string $search): Builder {
                        return $query->where('data->title', 'like', "%{$search}%");
                    })
                    ->icon(fn ($record) => $record->data['icon'] ?? null)
                    ->iconColor(fn ($record) => $record->data['iconColor'] ?? 'gray')
                    ->weight(fn ($record) => $record->read_at ? 'normal' : 'bold'),

                Tables\Columns\TextColumn::make('data.body')
                    ->label('내용')
                    ->limit(80)
                    ->searchable(query: function (Builder $query, string $search): Builder {
                        return $query->where('data->body', 'like', "%{$search}%");
                    })
                    ->color(fn ($record) => $record->deleted_at ? 'gray' : null),

                Tables\Columns\TextColumn::make('status')
                    ->label('상태')
                    ->badge()
                    ->getStateUsing(function ($record) {
                        if ($record->deleted_at) return '삭제됨';
                        if ($record->read_at) return '읽음';
                        return '안읽음';
                    })
                    ->color(fn (string $state): string => match ($state) {
                        '안읽음' => 'info',
                        '읽음' => 'gray',
                        '삭제됨' => 'danger',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('수신일시')
                    ->dateTime('Y-m-d H:i')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('상태')
                    ->options([
                        'unread' => '안읽음',
                        'read' => '읽음',
                        'deleted' => '삭제됨',
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return match ($data['value'] ?? null) {
                            'unread' => $query->whereNull('read_at')->whereNull('deleted_at'),
                            'read' => $query->whereNotNull('read_at')->whereNull('deleted_at'),
                            'deleted' => $query->whereNotNull('deleted_at'),
                            default => $query,
                        };
                    }),
            ])
            ->actions([
                Tables\Actions\Action::make('markAsRead')
                    ->label('읽음 처리')
                    ->icon('heroicon-m-check')
                    ->action(fn ($record) => $record->update(['read_at' => now()]))
                    ->visible(fn ($record) => !$record->read_at && !$record->deleted_at)
                    ->size('sm'),

                Tables\Actions\Action::make('restore')
                    ->label('복원')
                    ->icon('heroicon-m-arrow-uturn-left')
                    ->color('success')
                    ->action(fn ($record) => $record->restore())
                    ->visible(fn ($record) => $record->deleted_at !== null)
                    ->requiresConfirmation()
                    ->modalHeading('알림 복원')
                    ->modalDescription('이 알림을 복원하시겠습니까?')
                    ->size('sm'),
            ])
            ->bulkActions([
                Tables\Actions\BulkAction::make('markAllRead')
                    ->label('읽음 처리')
                    ->icon('heroicon-m-check')
                    ->action(fn ($records) => $records->each(fn ($r) => $r->update(['read_at' => now()])))
                    ->deselectRecordsAfterCompletion(),
            ])
            ->striped()
            ->paginated([10, 25, 50])
            ->emptyStateHeading('알림이 없습니다')
            ->emptyStateDescription('수신된 알림이 여기에 표시됩니다.')
            ->emptyStateIcon('heroicon-o-bell-slash');
    }

    public static function canAccess(): bool
    {
        return (bool) auth()->user();
    }
}
