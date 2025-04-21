<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Filament\Resources\ProductResource\RelationManagers;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ToggleButtons;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-cube';

    protected static ?string $navigationLabel = 'Produk';

    protected static ?string $label = 'Produk';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Tabs::make('Tabs')
                    ->tabs([
                        Tabs\Tab::make('Informasi')
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
                        Tabs\Tab::make('Variasi')
                            ->schema([
                                Repeater::make('customizations')
                                    ->collapsible()
                                    ->cloneable()
                                    ->label('Daftar Variasi')
                                    ->relationship()
                                    ->schema([
                                        TextInput::make('customization_type')
                                            ->label('Tipe Kustomisasi')
                                            ->required()
                                            ->live(onBlur: true),
                                        TextInput::make('customization_value')
                                            ->label('Nilai/Ukuran')
                                            ->required(),
                                        Textarea::make('description')
                                            ->label('Deskripsi')
                                            ->columnSpanFull(),
                                        TextInput::make('price')
                                            ->label('Harga')
                                            ->required(),
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
                                    ])
                                    ->columns(2)
                                    ->itemLabel(fn (array $state): ?string => $state['customization_type'] ?? 'Variasi')
                            ]),
                        Tabs\Tab::make('Stok')
                            ->schema([
                                Hidden::make('available_stock_count')
                                    ->disabled(),
                                Repeater::make('stocks')
                                    ->collapsible()
                                    ->cloneable()
                                    ->label('Stok Produk')
                                    ->relationship()
                                    ->schema([
                                        DatePicker::make('stock_in_date')
                                            ->label('Tanggal Masuk Stok')
                                            ->required()
                                            ->default(now())
                                            ->live(onBlur: true),
                                        DatePicker::make('stock_out_date')
                                            ->label('Tanggal Keluar Stok')
                                            ->helperText('Kosongkan jika belum keluar stok'),
                                        DatePicker::make('expiry_date')
                                            ->label('Tanggal Kadaluarsa')
                                            ->required()
                                            ->default(now()->addDays(7)),
                                    ])
                                    ->columns(3)
                                    ->itemLabel(fn (array $state): ?string => $state['stock_in_date'] ?? 'Stok Produk')
                                    ->defaultItems(0)
                            ])
                        ])
                        ->columnSpanFull(),
                        
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('image')
                    ->label('Foto Produk')
                    ->size(50)
                    ->searchable(),
                TextColumn::make('name')
                    ->label('Nama Produk')
                    ->searchable()
                    ->sortable()
                    ->description(fn (Product $record): string => $record->description)
                    ->limit(50)
                    ->wrap(),
                TextColumn::make('price')
                    ->label('Harga')
                    ->money('IDR')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('available_stock_count')
                    ->label('Stok Tersedia')
                    ->sortable()
                    ->alignCenter(),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'active' => 'success',
                        'inactive' => 'danger',
                    })
                    ->hidden(!auth()->user()->isAdmin())
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
                    // DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageProducts::route('/'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        if (auth()->user()->isAdmin()) {
            // If the user is an admin, return all products
            return parent::getEloquentQuery();
        }

        // Filter products based on the user's role
        // return parent::getEloquentQuery()->whereHas('stocks', function (Builder $query) {
        //     $query->where('stock_out_date', null);
        // });
        return parent::getEloquentQuery()->where('status', 'active');
    }
}
