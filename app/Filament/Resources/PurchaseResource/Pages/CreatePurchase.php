<?php

namespace App\Filament\Resources\PurchaseResource\Pages;

use App\Filament\Resources\PurchaseResource;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Resources\Pages\CreateRecord;

class CreatePurchase extends CreateRecord
{
    protected static string $resource = PurchaseResource::class;

    protected function getFormActions(): array
    {
        return [
            Action::make('create')
                ->label("Selanjutnya")
                ->submit('create')
                ->keyBindings(['mod+s'])
        ];
    }

    protected function getRedirectUrl(): string
    {
        return route('filament.admin.resources.purchase-items.create', [
            'purchase_id' => $this->record->id,
        ]);
    }
}
