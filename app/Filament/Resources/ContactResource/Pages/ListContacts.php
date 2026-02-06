<?php

namespace App\Filament\Resources\ContactResource\Pages;

use App\Filament\Resources\ContactResource;
use App\Models\Contact;
use Filament\Actions;
use Filament\Infolists\Concerns\InteractsWithInfolists;
use Filament\Infolists\Contracts\HasInfolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ListRecords;

class ListContacts extends ListRecords implements HasInfolists
{
    use InteractsWithInfolists;

    protected static string $resource = ContactResource::class;

    protected static string $view = 'filament.resources.contact-resource.pages.list-contacts';

    public bool $slideOverMode = true;

    public ?int $selectedRecordId = null;

    public ?Contact $selectedRecord = null;

    public function selectRecord(int $recordId): void
    {
        if ($this->slideOverMode) {
            $this->selectedRecordId = $recordId;
            $this->selectedRecord = Contact::find($recordId);
            $this->dispatch('record-selected');
        } else {
            $this->redirect(ContactResource::getUrl('edit', ['record' => $recordId]));
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

        return ContactResource::infolist($infolist)
            ->record($this->selectedRecord);
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
