<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RoleResource\Pages;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Spatie\Permission\Models\Role;

class RoleResource extends Resource
{
    protected static ?string $model = Role::class;

    protected static ?string $navigationIcon = 'heroicon-o-shield-check';

    protected static ?int $navigationSort = 98;

    public static function getNavigationGroup(): ?string
    {
        return __('navigation.groups.system_settings');
    }

    public static function getNavigationLabel(): string
    {
        return __('navigation.labels.role');
    }

    public static function getModelLabel(): string
    {
        return __('models.role');
    }

    public static function getPluralModelLabel(): string
    {
        return __('models.role_plural');
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label(__('fields.role_name'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('permissions_count')
                    ->label(__('fields.permissions_count'))
                    ->counts('permissions')
                    ->sortable(),
                Tables\Columns\TextColumn::make('users_count')
                    ->label(__('fields.users_count'))
                    ->counts('users')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('fields.created_at'))
                    ->dateTime('Y.m.d')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->url(fn (Role $record): string => static::getUrl('edit', ['record' => $record]))
                    ->visible(fn (Role $record): bool => $record->name !== 'Super Admin'),
            ])
            ->bulkActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRoles::route('/'),
            'edit' => Pages\EditRole::route('/{record}/edit'),
        ];
    }

    public static function canAccess(): bool
    {
        $user = auth()->user();

        return $user && $user->hasRole('Super Admin');
    }

    public static function shouldRegisterNavigation(): bool
    {
        return static::canAccess();
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canDelete(\Illuminate\Database\Eloquent\Model $record): bool
    {
        return false;
    }
}
