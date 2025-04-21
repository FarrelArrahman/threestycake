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

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $label = 'Pesanan';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Tabs::make('Tabs')
                    ->tabs([
                        Tabs\Tab::make('Detail Pesanan')
                            ->schema([
                                TextInput::make('name')
                                    ->label('Nama Produk')
                                    ->required()
                                    ->minLength(3)
                                    ->columnSpanFull(),
                                Textarea::make('description')
                                    ->label('Deskripsi')
                                    ->columnSpanFull(),
                                TextInput::make('price')
                                    ->label('Harga')
                                    ->required(),
                                FileUpload::make('image')
                                    ->label('Foto Produk')
                                    ->image()
                                    ->columnSpanFull(),
                                ToggleButtons::make('status')
                                    ->label('Status')
                                    ->options([
                                        'active' => 'Aktif',
                                        'inactive' => 'Tidak Aktif',
                                    ])
                                    ->colors([
                                        'active' => 'success',
                                        'inactive' => 'danger',
                                    ])
                                    ->icons([
                                        'active' => 'heroicon-o-check',
                                        'inactive' => 'heroicon-o-x-mark',
                                    ])
                                    ->default('active')
                                    ->inline()
                                    ->grouped(),
                            ]),
                        Tabs\Tab::make('Produk Pesanan')
                            ->schema([
                                Repeater::make('orderItems')
                                    ->collapsible()
                                    ->cloneable()
                                    ->label('Daftar Produk')
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
                                                ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                                    $product = \App\Models\Product::find($state);
                                                    $set('selected_product_id', $state); // Simpan ke field global/level atas
                                                    $set('price', $product->price); // Simpan ke field global/level atas
                                                    $set('available_stock', $product->available_stock->count()); // Simpan ke field global/level atas
                                                }
                                                )
                                            ->required()
                                            ->searchable()
                                            ->live(onBlur: true)
                                            ->disableOptionsWhenSelectedInSiblingRepeaterItems(),
                                        TextInput::make('price')
                                            ->label('Harga')
                                            ->required(),
                                        TextInput::make('quantity')
                                            ->label('Jumlah')
                                            ->numeric()
                                            ->minValue(1)
                                            ->maxValue(5)
                                            ->required()
                                            ->reactive()
                                            ->afterStateUpdated(function ($state, Set $set, Get $get) {
                                                $set('total', $state * $get('price'));
                                            }),
                                        TextInput::make('total')
                                            ->label('Subtotal')
                                            ->required()
                                            ->disabled(),
                                        Textarea::make('custom_note')
                                            ->label('Catatan Tambahan')
                                            ->columnSpanFull(),
                                        Repeater::make('customizations')
                                            ->label('Kustomisasi')
                                            ->relationship()
                                            ->schema([
                                                Select::make('product_customization_id')
                                                    ->label('Kustomisasi')
                                                    ->options(function (callable $get) {
                                                        $productId = $get('../../selected_product_id'); // Naik ke parent
                                                        if (!$productId) return [];
                                        
                                                        return \App\Models\ProductStock::find($productId)
                                                            ->product
                                                            ->customizations
                                                            ->pluck('customization_type', 'id')
                                                            ->toArray();
                                                    })
                                                    ->disabled(fn (callable $get) => !$get('../../selected_product_id'))
                                                    ->required()
                                                    ->searchable(),
                                                TextInput::make('value')
                                                    ->label('Nilai/Ukuran')
                                                    ->required(),
                                            ])
                                            ->columns(2)
                                            ->columnSpanFull(),
                                    ])
                                    ->columns(4)
                                    ->itemLabel(fn (array $state): ?string => $state['customization_type'] ?? 'Variasi')
                            ]),
                        ])
                        ->columnSpanFull(),
            ]);
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
        ];
    }
}
