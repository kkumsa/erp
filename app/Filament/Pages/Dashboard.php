<?php

namespace App\Filament\Pages;

use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    protected static ?string $navigationIcon = 'heroicon-o-home';

    public function getTitle(): string|\Illuminate\Contracts\Support\Htmlable
    {
        return __('common.pages.dashboard');
    }

    public static function getNavigationLabel(): string
    {
        return __('navigation.labels.dashboard');
    }

    public function getColumns(): int | string | array
    {
        return 4;
    }
}
