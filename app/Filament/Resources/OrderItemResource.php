<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderItemResource\Pages;
use App\Filament\Resources\OrderItemResource\RelationManagers;
use App\Filament\Resources\OrderItemResource\RelationManagers\OrderItemCustomizationRelationManager;
use App\Models\Order;
use App\Models\OrderItem;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Guava\FilamentNestedResources\Ancestor;
use Guava\FilamentNestedResources\Concerns\NestedResource;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class OrderItemResource extends Resource
{
    use NestedResource;

    protected static ?string $model = OrderItem::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $label = 'Item Pesanan';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Hidden::make('order_id')
                    ->default(request()->route('record')),
                Forms\Components\Hidden::make('hidden_name')
                    ->hiddenOn('create')
                    ->dehydrated(false)
                    ->afterStateHydrated(function ($state, Set $set, callable $get, $record) {
                        if( ! $record) return;

                        $availableStock = $record->product->available_stock->count();
                        $reservedStock = $record->order->orderItems
                            ->where('product_id', $record->product_id)
                            ->sum('quantity');
                        $currentStock = $availableStock - $reservedStock + $record->quantity;

                        $set('price', $record->product->price);
                        $set('available_stock', $currentStock);
                        $set('total_price', $get('quantity') * $get('price'));
                    }),
                Forms\Components\Placeholder::make('name')
                    ->content(fn ($record) => $record?->product?->name)
                    ->label('Nama Produk')
                    ->extraAttributes(['style' => 'font-size: 1.5rem; font-weight: bold;'])
                    ->hiddenOn('create'),
                Forms\Components\Select::make('product_id')
                    ->options(\App\Models\ProductStock::with('product')
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
                    ->get()->pluck('product.name', 'product.id'))
                    ->label('Nama Produk')
                    ->required()
                    ->reactive()
                    ->afterStateUpdated(function ($state, Set $set, callable $get) {
                        $order = Order::find($get('order_id'));
                        $availableStock = \App\Models\Product::find($state)->available_stock;
                        $reservedStock = $order->orderItems->where('product_id', $state)->sum('quantity');
                        $currentStock = $availableStock->count() - $reservedStock;

                        $set('price', \App\Models\Product::find($state)->price);
                        $set('available_stock', $currentStock);
                        $set('total_price', $get('quantity') * $get('price'));
                    })
                    ->hiddenOn(['view','edit']),
                Forms\Components\Placeholder::make('price')
                    ->content(fn ($record, callable $get) => 'Rp ' . number_format($record?->productStock?->product?->price ?? $get('price'), 0, ',', '.'))
                    ->label('Harga per Produk')
                    ->extraAttributes(['style' => 'font-size: 1.5rem; font-weight: bold;'])
                    ->reactive()
                    ->dehydrated(),
                Forms\Components\Placeholder::make('quantity_placeholder')
                    ->content(fn ($record, callable $get) => $record?->quantity)
                    ->label('Jumlah Produk')
                    ->extraAttributes(['style' => 'font-size: 1.5rem; font-weight: bold;'])
                    ->reactive()
                    ->dehydrated(false)
                    ->hiddenOn(['create', 'edit']),
                Forms\Components\TextInput::make('quantity')
                    ->numeric()
                    ->label('Jumlah Produk')
                    ->required()
                    ->reactive()
                    ->default(1)
                    ->minValue(1)
                    ->maxValue(fn ($record, callable $get) => $get('available_stock') ?? $record?->productStock?->available_stock->count())
                    ->helperText(fn ($record, callable $get) => 'Stok yang dapat dipesan: ' . $get('available_stock') ?? $record?->productStock?->available_stock->count())
                    ->afterStateUpdated(function ($state, Set $set, callable $get) {
                        $set('total_price', $state * $get('price'));
                    })
                    ->afterStateHydrated(function ($state, Set $set, callable $get) {
                        $set('total_price', $state * $get('price'));
                    })
                    ->hiddenOn(['view']),
                Forms\Components\Placeholder::make('total_price')
                    ->content(fn (callable $get) => 'Rp ' . number_format($get('total_price'), 0, ',', '.'))
                    ->label('Total Harga')
                    ->extraAttributes(['style' => 'font-size: 1.5rem; font-weight: bold;'])
                    ->reactive(),
            ])
            ->columns(4);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            OrderItemCustomizationRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrderItems::route('/'),
            'create' => Pages\CreateOrderItem::route('/create'),
            'edit' => Pages\EditOrderItem::route('/{record}/edit'),
            'view' => Pages\ViewOrderItem::route('/{record}'),
        ];
    }

    public static function getAncestor(): ?Ancestor
    {
        return Ancestor::make(
            'orderItems',
            'order'
        );
    }

    public static function getBreadcrumbRecordLabel(OrderItem $record)
    {
        return $record->product->name;
    }
}
