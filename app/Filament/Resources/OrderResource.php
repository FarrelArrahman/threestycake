<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderResource\Pages;
use App\Filament\Resources\OrderResource\RelationManagers;
use App\Models\Order;
use App\Models\ProductStock;
use Filament\Tables\Actions\Action;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
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
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Storage;

class OrderResource extends Resource
{
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
                        Tabs\Tab::make('Produk Pesanan')
                            ->schema([
                                Repeater::make('orderItems')
                                    ->collapsible()
                                    ->cloneable()
                                    ->label('Produk')
                                    ->relationship()
                                    ->schema([
                                        Select::make('product_stock_id')
                                            ->label('Produk')
                                            ->options(ProductStock::with('product')
                                                ->whereHas('product', function (Builder $query) {
                                                    $query->where('status', 'active');
                                                })
                                                ->whereNull('stock_out_date')
                                                ->whereIn('id', function ($query) {
                                                    $query->selectRaw('MIN(id)')
                                                        ->from('product_stocks')
                                                        ->whereNull('stock_out_date')
                                                        ->groupBy('product_id');
                                                })
                                                ->get()
                                                ->filter(fn ($stock) => $stock->product && $stock->product->name)->pluck('product.name', 'id'))
                                                ->reactive()
                                                ->afterStateUpdated(function ($state, callable $set, callable $get) use ($updateTotals, $updateGrandTotal) {
                                                    $stock = \App\Models\ProductStock::find($state);
                                                    if (!$stock) return;
                                                    $set('selected_product_id', $state);
                                                    $set('price', $stock->product->price);
                                                    $set('available_stock', $stock->product->available_stock->count());
                                                    $set('quantity', 1); // reset jumlah

                                                    $updateTotals($get, $set);
                                                    // $updateGrandTotal($get, $set);
                                                }
                                                )
                                            ->required()
                                            ->searchable()
                                            ->live(onBlur: true)
                                            ->disableOptionsWhenSelectedInSiblingRepeaterItems(),
                                        TextInput::make('price')
                                            ->label('Harga')
                                            ->readOnly()
                                            ->required(),
                                        TextInput::make('quantity')
                                            ->label('Jumlah')
                                            ->type('number')
                                            ->numeric()
                                            ->minValue(1)
                                            ->step(1)
                                            ->extraAttributes(function (callable $get) {
                                                $max = $get('available_stock');
                                                return ['max' => $max];
                                            })
                                            ->required()
                                            ->reactive()
                                            ->afterStateUpdated(function ($state, callable $set, callable $get) use ($updateTotals, $updateGrandTotal) {
                                                $custom = $get('customizations_total') ?? 0;
                                                $set('total', ($state * $get('price')) + $custom);
                                                // $updateTotals($get, $set);
                                                // $updateGrandTotal($get, $set);
                                            }),
                                        TextInput::make('total')
                                            ->label('Subtotal')
                                            ->disabled()
                                            ->dehydrated(false)
                                            ->reactive(),
                                        Textarea::make('custom_note')
                                            ->label('Catatan Tambahan')
                                            ->columnSpanFull(),
                                        Repeater::make('customizations')
                                            ->label('Kustomisasi')
                                            ->helperText('Hapus atau kosongkan jika tidak ingin menambahkan kustomisasi')
                                            ->relationship()
                                            ->schema([
                                                Select::make('product_customization_id')
                                                    ->label('Tipe')
                                                    ->options(function (callable $get) {
                                                        $productId = $get('../../selected_product_id'); // Naik ke parent
                                                        if (!$productId) return [];
                                        
                                                        return \App\Models\ProductStock::find($productId)
                                                            ->product
                                                            ->customizations
                                                            ->where('status', 'active')
                                                            ->pluck('customization_type', 'id')
                                                            ->toArray();
                                                    })
                                                    ->disabled(fn (callable $get) => !$get('../../selected_product_id'))
                                                    ->required()
                                                    ->searchable()
                                                    ->reactive()
                                                    ->afterStateUpdated(function ($state, callable $set, callable $get) use ($updateTotals, $updateGrandTotal) {
                                                        $customization = \App\Models\ProductCustomization::find($state);
                                                        if (!$customization) return;

                                                        $set('selected_customization_id', $state);
                                                        $set('price', $customization->price);
                                                        $set('customization_value', 1);
                                                        $set('subtotal', $customization->price * 1); // default quantity

                                                        // $updateTotals($get, $set);
                                                        // $updateGrandTotal($get, $set);
                                                    }
                                                    )
                                                    ->disableOptionsWhenSelectedInSiblingRepeaterItems(),
                                                TextInput::make('price')
                                                    ->label('Harga')
                                                    ->readOnly()
                                                    ->required(),
                                                TextInput::make('customization_value')
                                                    ->label('Quantity')
                                                    ->numeric()
                                                    ->minValue(1)
                                                    ->step(1)
                                                    ->maxValue(1)
                                                    ->default(1)
                                                    ->required()
                                                    ->readOnly()
                                                    ->reactive()
                                                    ->afterStateUpdated(function ($state, callable $get, callable $set) use ($updateTotals) {
                                                        $custom = $get('customizations_total') ?? 0;
                                                        $set('subtotal', ($state * $get('price')) + $custom);
                                                        // $updateTotals($get, $set);
                                                    }),
                                                TextInput::make('subtotal')
                                                    ->label('Subtotal (Kustomisasi)')
                                                    ->disabled()
                                                    ->dehydrated(false)
                                            ])
                                            ->columns(4)
                                            ->columnSpanFull(),
                                    ])
                                    ->columns(4)
                                    ->itemLabel(fn (array $state): ?string => $state['customization_type'] ?? 'Variasi')
                                    ->reactive()
                                    ->afterStateUpdated(function (callable $get, callable $set) use ($updateTotals) {
                                        $updateTotals($get, $set);
                                    }),
                            ]),
                        ])
                        ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
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
                Tables\Actions\EditAction::make()
                    ->label('Edit')
                    ->icon('heroicon-c-pencil')
                    ->color('primary')
                    ->openUrlInNewTab()
                    ->hidden(function (Order $record) {
                        return auth()->user()?->customer?->id !== $record->customer_id || $record->status !== 'pending';
                    }),
                Tables\Actions\DeleteAction::make()
                    ->hidden(fn (Order $record): bool => $record->status !== 'pending'),
                Tables\Actions\Action::make('payment')
                    ->label('Bayar')
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
                    ->openUrlInNewTab()
                    ->hidden(fn (Order $record): bool => $record->status !== 'paid' && $record->status !== 'delivered'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    // Tables\Actions\DeleteBulkAction::make(),
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
            ->emptyStateHeading('Belum ada pesanan')
            ->emptyStateIcon('heroicon-c-shopping-bag')
            ->emptyStateDescription('Pesanan yang Anda buat akan muncul di sini. Anda dapat membuat pesanan baru dengan mengklik tombol "Buat Pesanan".');
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrders::route('/'),
            'create' => Pages\CreateOrder::route('/create'),
            'edit' => Pages\EditOrder::route('/{record}/edit'),
            'payment' => Pages\PaymentOrder::route('/{record}/payment'),
        ];
    }
}
