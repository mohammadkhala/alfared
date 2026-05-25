<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use Filament\Widgets\Widget;

class OrderStatsWidget extends Widget
{
    protected static string $view = 'filament.widgets.order-stats-widget';
    protected int|string|array $columnSpan = 'full';
    protected static ?int $sort = 0;

    protected function getViewData(): array
    {
        return [
            'new'        => Order::where('status', 'pending')->count(),
            'processing' => Order::whereIn('status', ['confirmed', 'processing'])->count(),
            'shipped'    => Order::where('status', 'shipped')->count(),
            'delivered'  => Order::where('status', 'delivered')->count(),
            'cancelled'  => Order::whereIn('status', ['cancelled', 'returned'])->count(),
        ];
    }
}
