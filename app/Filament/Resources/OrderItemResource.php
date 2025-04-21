<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderItemResource\Pages;
use App\Filament\Resources\OrderItemResource\RelationManagers;
use App\Models\OrderItem;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Grid;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class OrderItemResource extends Resource
{
    protected static ?string $model = OrderItem::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static bool $shouldRegisterNavigation = false;

    public static function form(Form $form): Form
    {
        $order = new \App\Models\Order();
        if (request()->has('order_id')) {
            $order = \App\Models\Order::find(request('order_id'));
        }

        return $form
            ->schema([
                    Grid::make(2)
                        ->schema([
                            Forms\Components\Select::make('product_id')
                                ->label('Produk')
                                ->options(\App\Models\Product::all()->pluck('name', 'id'))
                                ->required()
                                ->searchable()
                                ->reactive()
                                ->afterStateUpdated(function ($state, Set $set, Get $get) {
                                    $product = \App\Models\Product::find($state);
                                    $set('price', $product->price ?? '');
                                    $set('total', $get('quantity') * $get('price'));
                                }),
                            Forms\Components\TextInput::make('price')
                                ->label('Harga')
                                ->required()
                                ->readOnly(),
                            Forms\Components\TextInput::make('quantity')
                                ->label('Jumlah')
                                ->required()
                                ->numeric()
                                ->minValue(1)
                                ->default(0)
                                ->reactive()
                                ->afterStateUpdated(function ($state, Set $set, Get $get) {
                                    $set('total', $state * $get('price'));
                                }),
                            Forms\Components\TextInput::make('total')
                                ->label('Total')
                                ->disabled(),
                        ])->columns(4),
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
            ]);
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
            'index' => Pages\ListOrderItems::route('/'),
            'create' => Pages\CreateOrderItem::route('/create'),
            'edit' => Pages\EditOrderItem::route('/{record}/edit'),
        ];
    }
}
