<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\UserWelcomeWidget;
use Filament\Pages\Page;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-home';

    protected static string $view = 'filament.pages.dashboard';

    protected static ?string $title = 'Dashboard';

    protected static bool $shouldRegisterNavigation = false;

    use BaseDashboard\Concerns\HasFiltersForm;

    public function filtersForm(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()
                    ->schema([
                        DatePicker::make('startDate')
                            ->maxDate(fn (Get $get) => $get('endDate') ?: now())
                            ->default(now()->startOfMonth()),
                        DatePicker::make('endDate')
                            ->minDate(fn (Get $get) => $get('startDate') ?: now())
                            ->maxDate(now())
                            ->default(now()),
                    ])
                    ->columns(2)
                    ->hidden(! auth()->user()->isAdmin()),
            ]);
    }

    public function getColumns()
    {
        return 2;
    }

    public function getVisibleWidgets()
    {
        if (auth()->user()->isAdmin()) {
            return [
                \App\Filament\Widgets\StatsDashboard::class,
                \App\Filament\Widgets\SellingChart::class,
                \App\Filament\Widgets\BestSellingProductsChart::class,
            ];
        }

        // Kalau user biasa
        return [
            UserWelcomeWidget::class,
        ];
    }
}
