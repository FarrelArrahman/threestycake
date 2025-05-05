<?php

namespace App\Filament\Widgets;

use App\Models\OrderItem;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Illuminate\Support\Facades\DB;

class BestSellingProductsChart extends ChartWidget
{
    use InteractsWithPageFilters;
    
    protected static ?string $heading = 'Produk Terlaris';

    protected static ?string $maxHeight = '300px';

    protected function getData(): array
    {
        $startDate = ! is_null($this->filters['startDate'] ?? null) ?
            Carbon::parse($this->filters['startDate']) :
            now()->startOfMonth();

        $endDate = ! is_null($this->filters['endDate'] ?? null) ?
            Carbon::parse($this->filters['endDate']) :
            now();
        
        $topProducts = OrderItem::select('products.name', DB::raw('SUM(order_items.quantity) as total_sold'))
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->join('payments', 'orders.id', '=', 'payments.order_id')
            ->where('orders.status', 'paid')
            ->where('payments.status', 'confirmed')
            ->whereBetween('orders.order_date', [$startDate, $endDate])
            ->groupBy('products.name')
            ->orderByDesc('total_sold')
            ->limit(5)
            ->get();

        $colors = [
            '#FFB3BA', // pink pastel
            '#FFDFBA', // peach pastel
            '#FFFFBA', // kuning pastel
            '#BAFFC9', // hijau pastel
            '#BAE1FF', // biru pastel
            '#E2BAFF', // ungu pastel
            '#FFCCE5', // rose pastel
            '#D5F4E6', // aqua pastel
            '#F0E68C', // khaki soft
            '#C1E1C1', // hijau muda
        ];
    
        return [
            'datasets' => [
                [
                    'label' => 'Jumlah Terjual',
                    'data' => $topProducts->pluck('total_sold'),
                    'backgroundColor' => array_slice($colors, 0, $topProducts->count()),
                ],
            ],
            'labels' => $topProducts->pluck('name'),
        ];
    }

    protected function getType(): string
    {
        return 'pie';
    }

    protected function getFilters(): ?array
    {
        return [];
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'display' => true,
                    'position' => 'bottom',
                ],
            ],
            'scales' => [
                'x' => [
                    'display' => false,
                ],
                'y' => [
                    'display' => false,
                ],
            ],
        ];
    }
}