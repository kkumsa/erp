<?php

namespace App\Filament\Resources\ExpenseCategoryResource\Pages;

use App\Filament\Resources\ExpenseCategoryResource;
use App\Models\ExpenseCategory;
use Filament\Actions;
use Filament\Infolists\Concerns\InteractsWithInfolists;
use Filament\Infolists\Contracts\HasInfolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ListRecords;

class ListExpenseCategories extends ListRecords implements HasInfolists
{
    use InteractsWithInfolists;

    protected static string $resource = ExpenseCategoryResource::class;

    protected static string $view = 'filament.resources.expense-category-resource.pages.list-expense-categories';

    public bool $slideOverMode = true;

    public ?int $selectedRecordId = null;

    public ?ExpenseCategory $selectedRecord = null;

    public function selectRecord(int $recordId): void
    {
        if ($this->slideOverMode) {
            $this->selectedRecordId = $recordId;
            $this->selectedRecord = ExpenseCategory::find($recordId);
            $this->dispatch('record-selected');
        } else {
            $this->redirect(ExpenseCategoryResource::getUrl('edit', ['record' => $recordId]));
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

        return ExpenseCategoryResource::infolist($infolist)
            ->record($this->selectedRecord);
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
