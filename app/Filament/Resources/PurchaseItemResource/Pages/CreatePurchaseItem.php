<?php

namespace App\Filament\Resources\PurchaseItemResource\Pages;

use App\Filament\Resources\PurchaseItemResource;
use App\Filament\Resources\PurchaseItemResource\Widgets\PurchaseItemWidget;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Resources\Pages\CreateRecord;

class CreatePurchaseItem extends CreateRecord
{
    protected static string $resource = PurchaseItemResource::class;

    protected function getFormActions(): array
    {
        return [
            Action::make('create')
                ->label("Simpan")
                ->submit('create')
                ->keyBindings(['mod+s'])
        ];
    }

    protected function getRedirectUrl(): string
    {
        return route('filament.admin.resources.purchase-items.create', [
            'purchase_id' => $this->record->purchase_id,
        ]);
    }

    public function getFooterWidgets(): array
    {
        return [
            PurchaseItemWidget::make([
                'record' => request('purchase_id'),
            ]),
        ];
    }
    public function getFooterWidgetsColumns(): int | array
    {
        return 1;
    }
}
