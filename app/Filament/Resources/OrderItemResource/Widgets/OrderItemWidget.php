<?php

namespace App\Filament\Resources\OrderItemResource\Widgets;

use Filament\Forms\Components\TextInput;
use Filament\Tables;
use Filament\Tables\Columns\Summarizers\Summarizer;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Support\Facades\DB;

class OrderItemWidget extends BaseWidget
{
    public $orderId;

    public function mount($record)
    {
        $this->orderId = $record;
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                \App\Models\OrderItem::query()
                    ->where('order_id', $this->orderId)
            )
            ->columns([
                Tables\Columns\TextColumn::make('product.name')->label('Produk'),
                Tables\Columns\TextColumn::make('quantity')->label('Jumlah')
                    ->alignCenter(),
                Tables\Columns\TextColumn::make('price')->label('Harga')
                    ->money('idr')
                    ->alignEnd(),
                Tables\Columns\TextColumn::make('total')->label('Total')
                    ->getStateUsing(function ($record) {
                        return $record->quantity * $record->price;
                    })
                    ->money('idr')
                    ->alignEnd()
                    ->summarize(
                        Summarizer::make()
                            ->using(function ($query) {
                                return $query->sum(DB::raw('quantity * price'));
                            })
                            ->money('idr')
                    ),
            ])->actions([
                Tables\Actions\EditAction::make()
                    ->form([
                        TextInput::make('quantity')
                            ->label('Jumlah')
                            ->required()
                            ->numeric(),
                    ]),
                Tables\Actions\DeleteAction::make(),
            ]);
    }
}
