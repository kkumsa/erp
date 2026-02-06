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

    protected static ?string $navigationGroup = '시스템설정';

    protected static ?string $navigationLabel = '역할 관리';

    protected static ?string $modelLabel = '역할';

    protected static ?string $pluralModelLabel = '역할';

    protected static ?int $navigationSort = 98;

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('역할명')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('permissions_count')
                    ->label('권한 수')
                    ->counts('permissions')
                    ->sortable(),
                Tables\Columns\TextColumn::make('users_count')
                    ->label('사용자 수')
                    ->counts('users')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('생성일')
                    ->dateTime('Y-m-d')
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
        return false; // 역할은 시더에서만 생성
    }

    public static function canDelete(\Illuminate\Database\Eloquent\Model $record): bool
    {
        return false; // 역할 삭제 방지
    }
}
