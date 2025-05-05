<?php

namespace App\Filament\Resources\OrderResource\Pages;

use App\Filament\Resources\OrderResource;
use Filament\Actions\Action;
use Filament\Resources\Pages\Page;
use Guava\FilamentNestedResources\Concerns\NestedPage;
use Guava\FilamentNestedResources\Pages\CreateRelatedRecord;

class CreateOrderItem extends CreateRelatedRecord
{
    use NestedPage;

    protected static string $resource = OrderResource::class;

    protected static string $relationship = 'orderItems';

    protected function getFormActions(): array
    {
        return [
            $this->getCreateFormAction(),
            $this->getCancelFormAction(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return route('filament.admin.resources.orders.edit', [
            'record' => $this->getOwnerRecord()->id,
        ]);
    }

    protected function getCreateFormAction(): Action
    {
        return Action::make('create')
            ->label('Tambahkan ke Daftar Pesanan')
            ->submit('create')
            ->keyBindings(['mod+s']);
    }

    protected function getCancelFormAction(): Action
    {
        return Action::make('cancel')
            ->label('Batal')
            ->url(route('filament.admin.resources.orders.edit', ['record' => $this->getOwnerRecord()->id]))
            ->color('gray');
    }
}
