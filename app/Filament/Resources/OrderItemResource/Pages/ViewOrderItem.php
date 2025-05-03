<?php

namespace App\Filament\Resources\OrderItemResource\Pages;

use App\Filament\Resources\OrderItemResource;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Resources\Pages\ViewRecord;
use Guava\FilamentNestedResources\Concerns\NestedPage;

class ViewOrderItem extends ViewRecord
{
    use NestedPage;

    protected static string $resource = OrderItemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('back')
                ->label('Kembali')
                ->url(route('filament.admin.resources.orders.edit', ['record' => $this->record->order_id]))
                ->color('primary'),
        ];
    }
}
