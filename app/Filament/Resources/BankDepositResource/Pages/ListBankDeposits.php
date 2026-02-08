<?php

namespace App\Filament\Resources\BankDepositResource\Pages;

use App\Filament\Resources\BankDepositResource;
use App\Models\BankDeposit;
use Filament\Actions;
use Filament\Infolists\Concerns\InteractsWithInfolists;
use Filament\Infolists\Contracts\HasInfolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ListRecords;

class ListBankDeposits extends ListRecords implements HasInfolists
{
    use InteractsWithInfolists;

    protected static string $resource = BankDepositResource::class;

    protected static string $view = 'filament.resources.bank-deposit-resource.pages.list-bank-deposits';

    public bool $slideOverMode = true;

    public ?int $selectedRecordId = null;

    public ?BankDeposit $selectedRecord = null;

    public function selectRecord(int $recordId): void
    {
        if ($this->slideOverMode) {
            $this->selectedRecordId = $recordId;
            $this->selectedRecord = BankDeposit::find($recordId);
            $this->dispatch('record-selected');
        } else {
            $this->redirect(BankDepositResource::getUrl('edit', ['record' => $recordId]));
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

        return BankDepositResource::infolist($infolist)
            ->record($this->selectedRecord);
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
