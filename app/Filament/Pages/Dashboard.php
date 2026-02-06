<?php

namespace App\Filament\Pages;

use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    protected static ?string $navigationIcon = 'heroicon-o-home';

    protected static ?string $title = '대시보드';

    public function getColumns(): int | string | array
    {
        return 4;
    }
}
