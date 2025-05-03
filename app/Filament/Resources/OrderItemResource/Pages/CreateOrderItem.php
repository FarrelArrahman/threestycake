<?php

namespace App\Filament\Resources\OrderItemResource\Pages;

use App\Filament\Resources\OrderItemResource;
use App\Filament\Resources\OrderItemResource\Widgets\OrderItemWidget;
use App\Models\Order;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Resources\Pages\CreateRecord;
use Guava\FilamentNestedResources\Concerns\NestedPage;

class CreateOrderItem extends CreateRecord
{
    use NestedPage;
    protected static string $resource = OrderItemResource::class;

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
        return route('filament.admin.resources.order-items.create', [
            'order_id' => $this->record->order_id,
        ]);
    }
}
