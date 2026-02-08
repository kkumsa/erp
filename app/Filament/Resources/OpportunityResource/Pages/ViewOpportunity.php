<?php

namespace App\Filament\Resources\OpportunityResource\Pages;

use App\Filament\Resources\OpportunityResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewOpportunity extends ViewRecord
{
    protected static string $resource = OpportunityResource::class;

    protected static string $view = 'filament.pages.view-record-with-log';

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
