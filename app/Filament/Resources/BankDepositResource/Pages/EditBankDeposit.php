<?php

namespace App\Filament\Resources\BankDepositResource\Pages;

use App\Filament\Resources\BankDepositResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditBankDeposit extends EditRecord
{
    protected static string $resource = BankDepositResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
