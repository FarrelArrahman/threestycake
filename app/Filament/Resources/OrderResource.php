<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderResource\Pages;
use App\Filament\Resources\OrderResource\RelationManagers;
use App\Filament\Resources\OrderResource\RelationManagers\OrderItemsRelationManager;
use App\Models\Order;
use App\Models\ProductStock;
use Filament\Tables\Actions\Action;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ToggleButtons;
use Filament\Forms\Components\Wizard;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Page;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Guava\FilamentNestedResources\Ancestor;
use Guava\FilamentNestedResources\Concerns\NestedResource;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Storage;

class OrderResource extends Resource
{
    use NestedResource;

    protected static ?string $model = Order::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $label = 'Pesanan';

    public static function form(Form $form): Form
    {
        $updateTotals = function (callable $get, callable $set) {
            $customs = $get('customizations') ?? [];
            $customTotal = collect($customs)->sum(function ($c) {
                $custom = \App\Models\ProductCustomization::find($c['product_customization_id'] ?? null);
                return $custom?->price ?? 0;
            });
        
            $set('customizations_total', $customTotal);
        
            $price = $get('price') ?? 0;
            $qty = $get('quantity') ?? 1;
            $set('total', ($price * $qty) + $customTotal);
        };

        $updateGrandTotal = function (callable $get, callable $set) {
            $items = $get('orderItems') ?? [];
            $grandTotal = collect($items)->sum(fn ($item) => $item['total'] ?? 0);
            $set('grand_total', $grandTotal);
        };
        
        return $form
            ->schema([
                Tabs::make('Tabs')
                    ->tabs([
                        Tabs\Tab::make('Detail Pesanan')
                            ->schema([
                                DatePicker::make('order_date')
                                    ->label('Tanggal Pemesanan')
                                    ->required()
                                    ->default(now()),
                                Hidden::make('customer_id')
                                    ->default(fn () => auth()->user()?->customer?->id)
                                    ->required(),
                            ]),
                        ])
                        ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('order_code')
                    ->label('Kode Pesanan')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('order_date')
                    ->label('Tanggal Pemesanan')
                    ->dateTime('d/m/Y')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('customer.name')
                    ->label('Nama Pelanggan')
                    ->sortable()
                    ->searchable()
                    ->wrap()
                    ->hidden( ! auth()->user()->isAdmin()),
                TextColumn::make('total_price')
                    ->label('Total Harga')
                    ->money('IDR')
                    ->sortable(),
                TextColumn::make('total_quantity')
                    ->label('Jumlah Produk')
                    ->sortable(),
                TextColumn::make('status')
                    ->label('Status Pesanan')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'paid' => 'success',
                        'cancelled' => 'danger',
                        'delivered' => 'primary',
                    }),
                TextColumn::make('payment.status')
                    ->label('Status Pembayaran')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'confirmed' => 'success',
                        'rejected' => 'danger',
                        default => 'secondary',
                    })
            ])
            ->modifyQueryUsing(function (Builder $query) {
                $query->orderBy('created_at', 'desc');

                if (auth()->user()->isAdmin()) {
                    return;
                }
                $query->where('customer_id', auth()->user()?->customer?->id);
            })
            ->filters([
                Filter::make('pending')
                    ->label('Pesanan Baru')
                    ->query(fn (Builder $query): Builder => $query->where('status', 'pending'))
                    ->toggle(),
                Filter::make('paid')
                    ->label('Pesanan Dibayar')
                    ->query(fn (Builder $query): Builder => $query->where('status', 'paid'))
                    ->toggle(),
            ])
            ->actions([
                ActionGroup::make([
                    Tables\Actions\EditAction::make()
                    ->label('Ubah Pesanan')
                    ->icon('heroicon-c-pencil')
                    ->color('primary')
                    ->hidden(function (Order $record) {
                        return auth()->user()?->customer?->id !== $record->customer_id || $record->status !== 'pending';
                    }),
                Tables\Actions\DeleteAction::make()
                    ->hidden(fn (Order $record): bool => $record->status !== 'pending'),
                Tables\Actions\Action::make('payment')
                    ->label('Pembayaran')
                    ->url(fn (Order $record): string => route('filament.admin.resources.orders.payment', $record))
                    ->icon('heroicon-o-credit-card')
                    ->color('info')
                    ->openUrlInNewTab()
                    ->requiresConfirmation()
                    ->hidden(fn (Order $record): bool => ($record?->payment?->status === 'confirmed') && ! auth()->user()->isAdmin()),
                Tables\Actions\Action::make('view-payment')
                    ->label('Lihat Bukti')
                    ->url(fn (Order $record): string => Storage::url($record->payment->proof_image))
                    ->icon('heroicon-c-receipt-percent')
                    ->color('success')
                    ->hidden(fn (Order $record): bool => $record->status !== 'paid' && $record->status !== 'delivered'),
                Tables\Actions\ViewAction::make()
                ]),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    BulkAction::make('confirm')
                        ->label('Konfirmasi Pembayaran')
                        ->action(function (Collection $records) {
                            foreach ($records as $record) {
                                $record->update(['status' => 'paid']);
                                $record->payment()->update(['status' => 'confirmed']);
                                foreach ($record->orderItems as $item) {
                                    $item->productStock->stock_out_date = now();
                                    $item->productStock->save();
                                }
                            }

                            Notification::make()
                                ->title('Pembayaran berhasil dikonfirmasi!')
                                ->success()
                                ->send();
                        })
                        ->requiresConfirmation()
                        ->deselectRecordsAfterCompletion()
                        ->hidden(fn (Order $record): bool => $record->status === 'paid')
                        ->color('success')
                        ->icon('heroicon-o-check'),
                    BulkAction::make('cancel')
                        ->label('Batalkan Pembayaran')
                        ->action(function (Collection $records) {
                            foreach ($records as $record) {
                                $record->payment()->update(['status' => 'rejected']);
                            }

                            foreach ($record->orderItems as $item) {
                                $item->productStock->stock_out_date = null;
                                $item->productStock->save();
                            }

                            Notification::make()
                                ->title('Pembayaran berhasil ditolak!')
                                ->success()
                                ->send();
                        })
                        ->requiresConfirmation()
                        ->deselectRecordsAfterCompletion()
                        ->hidden(fn (Order $record): bool => $record->status === 'paid' || $record->status === 'pending')
                        ->color('danger')
                        ->icon('heroicon-o-x-mark'),
                ]),
            ])
            ->emptyStateActions([
                Action::make('create')
                    ->label('Buat Pesanan')
                    ->url(route('filament.admin.resources.orders.create'))
                    ->icon('heroicon-m-plus')
                    ->button(),
            ])
            ->emptyStateHeading('Tidak ada pesanan')
            ->emptyStateIcon('heroicon-c-shopping-bag')
            ->emptyStateDescription('Pesanan yang dibuat akan muncul di sini.');
    }

    public static function getRelations(): array
    {
        return [
            OrderItemsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrders::route('/'),
            'create' => Pages\CreateOrder::route('/create'),
            'edit' => Pages\EditOrder::route('/{record}/edit'),
            'view' => Pages\ViewOrder::route('/{record}'),
            'payment' => Pages\PaymentOrder::route('/{record}/payment'),

            'orderItems' => Pages\ManageOrderItems::route('/{record}/order-items'),
            'orderItems.create' => Pages\CreateOrderItem::route('/{record}/order-items/create'),
        ];
    }

    public static function getAncestor(): ?Ancestor
    {
        return null;
    }

    public static function getBreadcrumbRecordLabel(Order $record)
    {
        return $record->order_code;
    }
}
