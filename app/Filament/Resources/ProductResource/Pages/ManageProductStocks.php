<?php

namespace App\Filament\Resources\ProductResource\Pages;

use App\Filament\Resources\ProductResource;
use Filament\Actions;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Pages\ManageRelatedRecords;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ManageProductStocks extends ManageRelatedRecords
{
    protected static string $resource = ProductResource::class;

    protected static string $relationship = 'stocks';

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $title = 'Stok Produk';

    public static function getNavigationLabel(): string
    {
        return 'Stok Produk';
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Hidden::make('product_id')
                    ->default($this->record->id),
                Forms\Components\TextInput::make('quantity')
                    ->label('Jumlah Stok')
                    ->required()
                    ->numeric()
                    ->minValue(1)
                    ->default(1)
                    ->hiddenOn('edit'),
                Forms\Components\DatePicker::make('expiry_date')
                    ->label('Tanggal Kadaluarsa')
                    ->required()
                    ->minDate(now())
                    ->default(now()->addDays(7)),
                Forms\Components\DatePicker::make('stock_in_date')
                    ->label('Tanggal Masuk Stok')
                    ->required()
                    ->default(now()),
                Forms\Components\DatePicker::make('stock_out_date')
                    ->label('Tanggal Keluar Stok')
                    ->required()
                    ->default(now())
                    ->hiddenOn('create'),
            ])
            ->columns(2);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('product.name')
            ->columns([
                Tables\Columns\TextColumn::make('product.name')
                    ->label('Nama Produk'),
                Tables\Columns\TextColumn::make('expiry_date')
                    ->label('Tanggal Kadaluarsa')
                    ->date('d-m-Y')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('stock_in_date')
                    ->label('Tanggal Masuk Stok')
                    ->date('d-m-Y')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('stock_out_date')
                    ->label('Tanggal Keluar Stok')
                    ->date('d-m-Y')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\IconColumn::make('is_available')
                    ->boolean()
                    ->label('Tersedia')
                    ->alignCenter()
            ])
            ->defaultSort('stock_in_date', 'desc')
            ->filters([
                TernaryFilter::make('stock_out_date')
                    ->label('Stok Keluar')
                    ->placeholder('Semua')  // Opsional, default-nya 'Semua'
                    ->trueLabel('Ya')
                    ->falseLabel('Tidak')
                    ->queries(
                        true: fn ($query) => $query->whereNotNull('stock_out_date'),
                        false: fn ($query) => $query->whereNull('stock_out_date'),
                        blank: fn ($query) => $query, // default: tidak difilter
                    ),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Tambah Stok')
                    ->using(function (array $data, string $model): Model {
                        $productStock = [];
                        for($i = 0; $i < $data['quantity']; $i++) {
                            $productStock[] = [
                                'product_id' => $data['product_id'],
                                'expiry_date' => $data['expiry_date'],
                                'stock_in_date' => $data['stock_in_date'],
                            ];
                        }

                        $createdStocks = $this->getRelationship()->createMany($productStock);

                        return $createdStocks->last(); // ✅ Mengembalikan satu instance Model
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->label('Hapus Stok'),
                    BulkAction::make('stocks_out')
                        ->requiresConfirmation()
                        ->label('Keluarkan Stok')
                        ->color('danger')
                        ->icon('heroicon-s-arrow-up-tray')
                        ->action(fn (Collection $records) => $records->each->update([
                            'stock_out_date' => now(),
                        ]))
                        ->deselectRecordsAfterCompletion(),
                    BulkAction::make('stocks_in')
                        ->requiresConfirmation()
                        ->label('Masukkan Stok')
                        ->color('success')
                        ->icon('heroicon-s-arrow-down-tray')
                        ->action(fn (Collection $records) => $records->each->update([
                            'stock_out_date' => null,
                        ]))
                        ->deselectRecordsAfterCompletion()
                ]),
            ]);
    }
}
