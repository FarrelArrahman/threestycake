<?php

namespace App\Filament\Resources\ProductStockResource\Pages;

use App\Filament\Resources\ProductStockResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageProductStocks extends ManageRecords
{
    protected static string $resource = ProductStockResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
