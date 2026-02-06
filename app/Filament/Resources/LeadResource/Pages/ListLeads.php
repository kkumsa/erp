<?php

namespace App\Filament\Resources\LeadResource\Pages;

use App\Filament\Resources\LeadResource;
use App\Models\Lead;
use Filament\Actions;
use Filament\Infolists\Concerns\InteractsWithInfolists;
use Filament\Infolists\Contracts\HasInfolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ListRecords;

class ListLeads extends ListRecords implements HasInfolists
{
    use InteractsWithInfolists;

    protected static string $resource = LeadResource::class;

    protected static string $view = 'filament.resources.lead-resource.pages.list-leads';

    public bool $slideOverMode = true;

    public ?int $selectedRecordId = null;

    public ?Lead $selectedRecord = null;

    public function selectRecord(int $recordId): void
    {
        if ($this->slideOverMode) {
            $this->selectedRecordId = $recordId;
            $this->selectedRecord = Lead::find($recordId);
            $this->dispatch('record-selected');
        } else {
            $this->redirect(LeadResource::getUrl('view', ['record' => $recordId]));
        }
    }

    public function closePanel(): void
    {
        $this->selectedRecordId = null;
        $this->selectedRecord = null;
    }

    public function setSlideOverMode(bool $mode): void
    {
        $this->slideOverMode = $mode;

        if (!$mode) {
            $this->closePanel();
        }
    }

    public function recordInfolist(Infolist $infolist): Infolist
    {
        if (!$this->selectedRecord) {
            return $infolist->schema([]);
        }

        return LeadResource::infolist($infolist)
            ->record($this->selectedRecord);
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
