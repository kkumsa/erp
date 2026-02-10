<?php

namespace App\Filament\Resources\PurchaseOrderItemResource\Pages;

use App\Filament\Resources\PurchaseOrderItemResource;
use App\Models\PurchaseOrderItem;
use Filament\Actions;
use Filament\Infolists\Concerns\InteractsWithInfolists;
use Filament\Infolists\Contracts\HasInfolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ListRecords;

class ListPurchaseOrderItems extends ListRecords implements HasInfolists
{
    use InteractsWithInfolists;

    protected static string $resource = PurchaseOrderItemResource::class;

    protected static string $view = 'filament.resources.purchase-order-item-resource.pages.list-purchase-order-items';

    public bool $slideOverMode = false;

    public ?int $selectedRecordId = null;

    public ?PurchaseOrderItem $selectedRecord = null;

    public function selectRecord(int $recordId): void
    {
        if ($this->slideOverMode) {
            $this->selectedRecordId = $recordId;
            $this->selectedRecord = PurchaseOrderItem::find($recordId);
            $this->dispatch('record-selected');
        } else {
            $this->redirect(PurchaseOrderItemResource::getUrl('edit', ['record' => $recordId]));
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

        return PurchaseOrderItemResource::infolist($infolist)
            ->record($this->selectedRecord);
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
