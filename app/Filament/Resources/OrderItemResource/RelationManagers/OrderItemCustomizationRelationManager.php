<?php

namespace App\Filament\Resources\OrderItemResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class OrderItemCustomizationRelationManager extends RelationManager
{
    protected static string $relationship = 'customizations';

    protected static ?string $title = 'Kustomisasi';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('product_customization_id')
                    ->relationship(
                        name: 'productCustomization', 
                        titleAttribute: 'customization_type',
                        )
                    ->options(function($record) {
                        if( ! $record) {
                            return \App\Models\ProductCustomization::where('product_id', $this->getOwnerRecord()->product_id)->pluck('customization_type', 'id');
                        }

                        return $record->productCustomization->product->customizations->pluck('customization_type', 'id');
                    })
                    ->label('Tipe Kustomisasi')
                    ->required()
                    ->reactive()
                    ->afterStateUpdated(function ($state, callable $set) {
                        $customization = \App\Models\ProductCustomization::find($state);

                        if($customization) {
                            $set('price', $customization->price);
                        }
                    }),
                Forms\Components\Hidden::make('customization_value')
                    ->default(1),
                Forms\Components\TextInput::make('price')
                    ->label('Harga Kustomisasi')
                    ->required()
                    ->readOnly()
                    ->numeric(),
                Forms\Components\Textarea::make('custom_note')
                    ->label('Catatan Kustomisasi')
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('productCustomization.customization_type')
            ->columns([
                Tables\Columns\TextColumn::make('productCustomization.customization_type')
                    ->label('Tipe Kustomisasi')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('custom_note')
                    ->label('Catatan Kustomisasi')
                    ->wrap(),
                Tables\Columns\TextColumn::make('price')
                    ->label('Harga Kustomisasi (per qty)')
                    ->searchable()
                    ->sortable()
                    ->money('IDR', locale: 'id'),
                Tables\Columns\TextColumn::make('total_price')
                    ->label('Total Harga')
                    ->getStateUsing(function ($record) {
                        return $record->price * $record->orderItem->quantity;
                    })
                    ->searchable()
                    ->sortable()
                    ->money('IDR', locale: 'id'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->modalHeading('Tambah Kustomisasi')
                    ->createAnother(false),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->modalHeading('Ubah Kustomisasi'),
                Tables\Actions\DeleteAction::make()
                    ->modalHeading('Hapus Kustomisasi'),
                Tables\Actions\ViewAction::make()
                    ->modalHeading('Detail Kustomisasi'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateActions([
                Tables\Actions\CreateAction::make()
                    ->label('Tambah Kustomisasi'),
            ])
            ->emptyStateHeading('Tidak ada kustomisasi')
            ->emptyStateIcon('heroicon-c-pencil')
            ->emptyStateDescription('Jika ada, kustomisasi akan ditampilkan disini');
    }
}
