<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductCustomizationResource\Pages;
use App\Filament\Resources\ProductCustomizationResource\RelationManagers;
use App\Models\ProductCustomization;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ToggleButtons;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ProductCustomizationResource extends Resource
{
    protected static ?string $model = ProductCustomization::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Produk';

    protected static ?string $label = 'Kustomisasi';

    protected static ?string $navigationLabel = 'Kustomisasi';

    protected static bool $shouldRegisterNavigation = false;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('customization_type')
                    ->label('Tipe Kustomisasi')
                    ->required(),
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
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageProductCustomizations::route('/'),
        ];
    }
}
