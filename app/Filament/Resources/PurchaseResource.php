<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PurchaseResource\Pages;
use App\Filament\Resources\PurchaseResource\RelationManagers;
use App\Models\Purchase;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PurchaseResource extends Resource
{
    protected static ?string $model = Purchase::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static bool $shouldRegisterNavigation = false;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                DatePicker::make('purchase_date')
                    ->label('Tanggal Pembelian')
                    ->required()
                    ->default(now())
                    ->placeholder('Select a date')
                    ->columnSpanFull(),
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
                TextColumn::make('purchase_date')
                    ->label('Tanggal Pembelian')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('supplier.company_name')
                    ->label('Nama Perusahaan')
                    ->sortable()
                    ->searchable()
                    ->wrap(),
                TextColumn::make('supplier.name')
                    ->label('Nama Supplier')
                    ->sortable()
                    ->searchable()
                    ->wrap(),
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
            'index' => Pages\ListPurchases::route('/'),
            'create' => Pages\CreatePurchase::route('/create'),
            'edit' => Pages\EditPurchase::route('/{record}/edit'),
        ];
    }
}
