<?php

namespace App\Filament\Resources\ExpenseResource\Pages;

use App\Filament\Resources\ExpenseResource;
use App\Models\Expense;
use Filament\Actions;
use Filament\Infolists\Concerns\InteractsWithInfolists;
use Filament\Infolists\Contracts\HasInfolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ListRecords;

class ListExpenses extends ListRecords implements HasInfolists
{
    use InteractsWithInfolists;

    protected static string $resource = ExpenseResource::class;

    protected static string $view = 'filament.resources.expense-resource.pages.list-expenses';

    public bool $slideOverMode = true;

    public ?int $selectedRecordId = null;

    public ?Expense $selectedRecord = null;

    public function selectRecord(int $recordId): void
    {
        if ($this->slideOverMode) {
            $this->selectedRecordId = $recordId;
            $this->selectedRecord = Expense::find($recordId);
            $this->dispatch('record-selected');
        } else {
            $this->redirect(ExpenseResource::getUrl('view', ['record' => $recordId]));
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

        return ExpenseResource::infolist($infolist)
            ->record($this->selectedRecord);
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
