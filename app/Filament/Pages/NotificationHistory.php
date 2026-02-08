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
    protected static ?int $navigationSort = 2;
    protected static string $view = 'filament.pages.notification-history';

    public static function getNavigationGroup(): ?string
    {
        return __('navigation.groups.my_settings');
    }

    public static function getNavigationLabel(): string
    {
        return __('navigation.labels.notification_history');
    }

    public function getTitle(): string|\Illuminate\Contracts\Support\Htmlable
    {
        return __('common.pages.notification_history');
    }

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
                    ->label(__('fields.title'))
                    ->searchable(query: function (Builder $query, string $search): Builder {
                        return $query->where('data->title', 'like', "%{$search}%");
                    })
                    ->icon(fn ($record) => $record->data['icon'] ?? null)
                    ->iconColor(fn ($record) => $record->data['iconColor'] ?? 'gray')
                    ->weight(fn ($record) => $record->read_at ? 'normal' : 'bold'),

                Tables\Columns\TextColumn::make('data.body')
                    ->label(__('fields.content'))
                    ->limit(80)
                    ->searchable(query: function (Builder $query, string $search): Builder {
                        return $query->where('data->body', 'like', "%{$search}%");
                    })
                    ->color(fn ($record) => $record->deleted_at ? 'gray' : null),

                Tables\Columns\TextColumn::make('status')
                    ->label(__('fields.status'))
                    ->badge()
                    ->getStateUsing(function ($record) {
                        if ($record->deleted_at) return 'deleted';
                        if ($record->read_at) return 'read';
                        return 'unread';
                    })
                    ->formatStateUsing(fn (string $state) => __('common.statuses.' . $state))
                    ->color(fn (string $state): string => match ($state) {
                        'unread' => 'info',
                        'read' => 'gray',
                        'deleted' => 'danger',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('fields.received_at'))
                    ->dateTime('Y.m.d H:i')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label(__('fields.status'))
                    ->options([
                        'unread' => __('common.statuses.unread'),
                        'read' => __('common.statuses.read'),
                        'deleted' => __('common.statuses.deleted'),
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
                    ->label(__('common.buttons.mark_as_read'))
                    ->icon('heroicon-m-check')
                    ->action(fn ($record) => $record->update(['read_at' => now()]))
                    ->visible(fn ($record) => !$record->read_at && !$record->deleted_at)
                    ->size('sm'),

                Tables\Actions\Action::make('restore')
                    ->label(__('common.buttons.restore'))
                    ->icon('heroicon-m-arrow-uturn-left')
                    ->color('success')
                    ->action(fn ($record) => $record->restore())
                    ->visible(fn ($record) => $record->deleted_at !== null)
                    ->requiresConfirmation()
                    ->modalHeading(__('common.confirmations.notification_restore_heading'))
                    ->modalDescription(__('common.confirmations.notification_restore'))
                    ->size('sm'),
            ])
            ->bulkActions([
                Tables\Actions\BulkAction::make('markAllRead')
                    ->label(__('common.buttons.mark_as_read'))
                    ->icon('heroicon-m-check')
                    ->action(fn ($records) => $records->each(fn ($r) => $r->update(['read_at' => now()])))
                    ->deselectRecordsAfterCompletion(),
            ])
            ->striped()
            ->paginated([10, 25, 50])
            ->emptyStateHeading(__('common.empty_states.no_notifications'))
            ->emptyStateDescription(__('common.empty_states.notifications_description'))
            ->emptyStateIcon('heroicon-o-bell-slash');
    }

    public static function canAccess(): bool
    {
        return (bool) auth()->user();
    }
}
