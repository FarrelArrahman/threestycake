<?php

namespace App\Filament\Widgets;

use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;

class StatsDashboard extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total Customer', Customer::count()),
            Stat::make('Total Produk', Product::count()),
            Stat::make('Total Pesanan', Order::count()),
        ];
    }
}

class SalesChart extends ChartWidget
{
    protected static ?string $heading = 'Penjualan';

    public ?string $filter = 'daily';

    protected function getFilters(): ?array
    {
        return [
            'daily' => 'Harian',
            'monthly' => 'Bulanan',
            'yearly' => 'Tahunan',
        ];
    }

    protected function getData(): array
    {
        $query = DB::table('orders')
            ->join('order_items', 'orders.id', '=', 'order_items.order_id')
            ->join('payments', 'payments.order_id', '=', 'orders.id')
            ->where('payments.status', 'confirmed');

        if ($this->filter === 'daily') {
            $sales = $query
                ->selectRaw('DATE(orders.order_date) as label, SUM(order_items.price * order_items.quantity) as total')
                ->groupBy('label')
                ->orderBy('label')
                ->get();
        } elseif ($this->filter === 'monthly') {
            $sales = $query
                ->selectRaw('DATE_FORMAT(orders.order_date, "%Y-%m") as label, SUM(order_items.price * order_items.quantity) as total')
                ->groupBy('label')
                ->orderBy('label')
                ->get();
        } else {
            $sales = $query
                ->selectRaw('YEAR(orders.order_date) as label, SUM(order_items.price * order_items.quantity) as total')
                ->groupBy('label')
                ->orderBy('label')
                ->get();
        }

        return [
            'datasets' => [
                [
                    'label' => 'Total Penjualan',
                    'data' => $sales->pluck('total'),
                ],
            ],
            'labels' => $sales->pluck('label'),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}

class BestSellingProductsChart extends ChartWidget
{
    protected static ?string $heading = 'Produk Terlaris';

    protected function getData(): array
    {
        $data = DB::table('order_items')
            ->join('products', 'products.id', '=', 'order_items.product_id')
            ->selectRaw('products.name as label, SUM(order_items.quantity) as total')
            ->groupBy('products.name')
            ->orderByDesc('total')
            ->pluck('total', 'label')
            ->toArray();

        return [
            'datasets' => [
                [
                    'label' => 'Jumlah Terjual',
                    'data' => array_values($data),
                ],
            ],
            'labels' => array_keys($data),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    protected function getFilters(): ?array
    {
        return [
            'daily' => 'Harian',
            'monthly' => 'Bulanan',
            'yearly' => 'Tahunan',
        ];
    }
}

