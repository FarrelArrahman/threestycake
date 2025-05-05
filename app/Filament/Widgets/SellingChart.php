<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use App\Models\Payment;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;
use Illuminate\Support\Str;

class SellingChart extends ChartWidget
{
    use InteractsWithPageFilters;
    
    protected static ?string $heading = 'Penjualan';

    public ?string $filter = 'daily';

    protected static ?string $maxHeight = '300px';

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
        $trend = [];

        $startDate = ! is_null($this->filters['startDate'] ?? null) ?
            Carbon::parse($this->filters['startDate']) :
            now()->startOfMonth();

        $endDate = ! is_null($this->filters['endDate'] ?? null) ?
            Carbon::parse($this->filters['endDate']) :
            now();

        if ($this->filter === 'daily') {
            $trend = Trend::query(
                Order::selectRaw('DATE_FORMAT(orders.order_date, "%Y-%m-%d") as date, SUM(payments.amount) as aggregate')
                    ->join('payments', 'orders.id', '=', 'payments.order_id')
                    ->where('orders.status', 'paid')
                    ->where('payments.status', 'confirmed')
                    ->groupByRaw('DATE_FORMAT(orders.order_date, "%Y-%m-%d")')
            )
                ->between($startDate, $endDate)
                ->dateColumn('orders.order_date')
                ->perDay()
                ->sum('payments.amount');
        
        } elseif ($this->filter === 'monthly') {
            $trend = Trend::query(
                Order::selectRaw('DATE_FORMAT(orders.order_date, "%Y-%m") as date, SUM(payments.amount) as aggregate')
                    ->join('payments', 'orders.id', '=', 'payments.order_id')
                    ->where('orders.status', 'paid')
                    ->where('payments.status', 'confirmed')
                    ->groupByRaw('DATE_FORMAT(orders.order_date, "%Y-%m")')
            )
                ->between($startDate, $endDate)
                ->dateColumn('orders.order_date')
                ->perMonth()
                ->sum('payments.amount');
        
        } else { // yearly
            $trend = Trend::query(
                Order::selectRaw('DATE_FORMAT(orders.order_date, "%Y") as date, SUM(payments.amount) as aggregate')
                    ->join('payments', 'orders.id', '=', 'payments.order_id')
                    ->where('orders.status', 'paid')
                    ->where('payments.status', 'confirmed')
                    ->groupByRaw('DATE_FORMAT(orders.order_date, "%Y")')
            )
                ->between($startDate, $endDate)
                ->dateColumn('orders.order_date')
                ->perYear()
                ->sum('payments.amount');
        }

        $sales = $trend->map(function (TrendValue $value) {
            if ($this->filter === 'monthly') {
                $label = Carbon::parse($value->date)->translatedFormat('F Y');
            } elseif ($this->filter === 'yearly') {
                $label = $value->date;
            } else {
                $label = Carbon::parse($value->date)->translatedFormat('d M Y');
            }
        
            return [
                'label' => $label,
                'total' => $value->aggregate,
            ];
        });

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