<?php

namespace App\Filament\Resources\OrderResource\Pages;

use App\Filament\Resources\OrderResource;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Resources\Pages\ViewRecord;
use Guava\FilamentNestedResources\Concerns\NestedPage;

class ViewOrder extends ViewRecord
{
    use NestedPage;
    
    protected static string $resource = OrderResource::class;

    public function getHeaderActions(): array
    {
        return [
            Action::make('back')
                ->label('Kembali')
                ->url('/orders')
                ->color('primary'),
        ];
    }
}
