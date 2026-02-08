<?php

namespace App\Filament\Resources\BankDepositResource\Pages;

use App\Filament\Resources\BankDepositResource;
use Filament\Resources\Pages\CreateRecord;

class CreateBankDeposit extends CreateRecord
{
    protected static string $resource = BankDepositResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['created_by'] = auth()->id();

        return $data;
    }
}
