<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Filament\Resources\ProductResource\RelationManagers;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-cube';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('code')
                    ->label('Kode Produk')
                    ->required()
                    ->unique(ignoreRecord: true),
                TextInput::make('name')
                    ->label('Nama Produk')
                    ->required()
                    ->minLength(3),
                TextInput::make('price')
                    ->label('Harga'),
                TextInput::make('category')
                    ->label('Kategori')
                    ->required()
                    ->minLength(3),
                TextInput::make('stock')
                    ->label('Stok')
                    ->disabledOn('edit')
                    ->numeric()
                    ->required(),
                Select::make('unit')
                    ->label('Satuan')
                    ->options([
                        'pcs' => 'Pcs',
                        'box' => 'Box',
                        'pack' => 'Pack',
                    ])
                    ->default('pcs')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('code')
                    ->label('Kode Produk')
                    ->searchable(),
                TextColumn::make('name')
                    ->label('Nama Produk')
                    ->searchable(),
                TextColumn::make('price')
                    ->label('Harga')
                    ->searchable(),
                TextColumn::make('category')
                    ->label('Kategori')
                    ->searchable(),
                TextColumn::make('stock')
                    ->label('Stok'),
                TextColumn::make('unit')
                    ->label('Satuan'),
            ])
            ->filters([
                //
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageProducts::route('/'),
        ];
    }
}
