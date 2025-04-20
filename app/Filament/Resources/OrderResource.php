<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderResource\Pages;
use App\Filament\Resources\OrderResource\RelationManagers;
use App\Models\Order;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Select;
use Filament\Forms\Set;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('supplier_id')
                    ->label('Supplier')
                    ->options(\App\Models\Supplier::all()->pluck('company_name', 'id'))
                    ->required()
                    ->searchable()
                    ->createOptionForm(\App\Filament\Resources\SupplierResource::getForm())
                    ->createOptionUsing(function (array $data): int {
                        return \App\Models\Supplier::create($data)->id;
                    })
                    ->reactive()
                    ->afterStateUpdated(function ($state, Set $set) {
                        $supplier = \App\Models\Supplier::find($state);
                        $set('email', $supplier->email ?? '');
                    }),
                Forms\Components\TextInput::make('email')
                    ->label('Email')
                    ->email()
                    ->disabled(),
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
            'index' => Pages\ListOrders::route('/'),
            'create' => Pages\CreateOrder::route('/create'),
            'edit' => Pages\EditOrder::route('/{record}/edit'),
        ];
    }
}
