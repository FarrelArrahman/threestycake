<?php

namespace App\Filament\Resources\OrderResource\Pages;

use App\Filament\Resources\OrderResource;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Resources\Pages\EditRecord;
use Guava\FilamentNestedResources\Concerns\NestedPage;

class EditOrder extends EditRecord
{
    use NestedPage;
    
    protected static string $resource = OrderResource::class;

    protected static ?string $title = 'Detail Pesanan';

    public function mount(int|string $record): void
    {
        parent::mount($record);
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->successRedirectUrl(route('filament.admin.resources.orders.index')),
        ];
    }

    protected function getCancelFormAction(): Action
    {
        return Action::make('cancel')
            ->label('Batal')
            ->url(route('filament.admin.resources.orders.index'))
            ->color('gray');
    }
}
