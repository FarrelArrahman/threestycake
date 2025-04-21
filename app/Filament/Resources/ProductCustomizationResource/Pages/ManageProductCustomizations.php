<?php

namespace App\Filament\Resources\ProductCustomizationResource\Pages;

use App\Filament\Resources\ProductCustomizationResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageProductCustomizations extends ManageRecords
{
    protected static string $resource = ProductCustomizationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
