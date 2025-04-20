<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PurchaseItemResource\Pages;
use App\Filament\Resources\PurchaseItemResource\RelationManagers;
use App\Models\PurchaseItem;
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

class PurchaseItemResource extends Resource
{
    protected static ?string $model = PurchaseItem::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        $purchase = new \App\Models\Purchase();
        if (request()->has('purchase_id')) {
            $purchase = \App\Models\Purchase::find(request('purchase_id'));
        }

        return $form
            ->schema([
                Grid::make()
                    ->schema([
                        Forms\Components\Hidden::make('purchase_id')
                            ->default($purchase->id ?? request('purchase_id')),
                        DatePicker::make('purchase_date')
                            ->label('Tanggal Pembelian')
                            ->required()
                            ->default($purchase->purchase_date ?? now())
                            ->placeholder('Select a date')
                            ->disabled(),
                        Forms\Components\TextInput::make('supplier_id')
                            ->label('Supplier')
                            ->required()
                            ->disabled()
                            ->default($purchase->supplier?->name ?? ''),
                        Forms\Components\TextInput::make('email')
                            ->label('Email Supplier')
                            ->required()
                            ->disabled()
                            ->default($purchase->supplier?->email ?? ''),
                    ])->columns(3),
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
            'index' => Pages\ListPurchaseItems::route('/'),
            'create' => Pages\CreatePurchaseItem::route('/create'),
            'edit' => Pages\EditPurchaseItem::route('/{record}/edit'),
        ];
    }
}
