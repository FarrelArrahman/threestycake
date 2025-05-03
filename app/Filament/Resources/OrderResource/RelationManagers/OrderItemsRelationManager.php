<?php

namespace App\Filament\Resources\OrderResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Guava\FilamentNestedResources\Concerns\NestedRelationManager;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class OrderItemsRelationManager extends RelationManager
{
    use NestedRelationManager;

    protected static string $relationship = 'orderItems';

    protected static ?string $title = 'Produk yang dipesan';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('productStock.product.name')
                    ->label('Nama Produk')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('productStock.product.price')
                    ->label('Harga Produk')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('quantity')
                    ->label('Jumlah')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('product.name')
            ->columns([
                Tables\Columns\TextColumn::make('product.name')
                    ->label('Nama Produk')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('product.price')
                    ->label('Harga Produk')
                    ->searchable()
                    ->sortable()
                    ->money('IDR', locale: 'id'),
                Tables\Columns\TextColumn::make('quantity')
                    ->label('Jumlah')
                    ->alignCenter(),
                Tables\Columns\TextColumn::make('customization_sentences')
                    ->label('Kustomisasi')
                    ->html()
                    ->limit(50)
                    ->wrap(),
                Tables\Columns\TextColumn::make('total_customization_price')
                    ->label('Harga Kustomisasi')
                    ->money('IDR', locale: 'id'),
                Tables\Columns\TextColumn::make('total_price_with_customization')
                    ->label('Total Harga')
                    ->money('IDR', locale: 'id'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Tambah Produk'),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->modalHeading('Ubah produk yang dipesan'),
                Tables\Actions\DeleteAction::make()
                    ->modalHeading('Hapus produk yang dipesan'),
                Tables\Actions\ViewAction::make()
                    ->modalHeading('Lihat produk yang dipesan'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateHeading('Belum ada produk yang dipesan')
            ->emptyStateDescription('Produk yang dipesan akan muncul di sini.');
    }
}
