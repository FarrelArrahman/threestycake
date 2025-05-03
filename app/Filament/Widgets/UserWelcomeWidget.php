<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;
use App\Models\Order;
use App\Models\Setting;

class UserWelcomeWidget extends Widget
{
    protected static string $view = 'filament.widgets.user-welcome-widget';

    protected int | string | array $columnSpan = 'full'; // supaya full lebar
    protected static bool $isLazy = false; // biar langsung load, tidak pakai lazy load

    public $orderCount;
    public $pendingOrders;
    public $settings;

    public function mount(): void
    {
        $user = auth()->user();

        // Hitung pesanan user
        $this->orderCount = Order::where('customer_id', $user->customer->id ?? null)->count();
        $this->pendingOrders = Order::where('customer_id', $user->customer->id ?? null)
            ->where('status', 'pending')
            ->count();
        $this->settings = Setting::all();
    }
}
//     public function getTitle(): string