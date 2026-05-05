<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\BazarStatsOverview;
use App\Filament\Widgets\OrdersChartWidget;
use App\Filament\Widgets\PendingOrdersWidget;
use App\Filament\Widgets\RecentOrdersWidget;
use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-home';

    protected static ?string $navigationLabel = 'Inicio';

    protected static ?string $title = 'Dashboard';

    public function getHeaderWidgets(): array
    {
        return [
            BazarStatsOverview::class,
            OrdersChartWidget::class,
            PendingOrdersWidget::class,
            RecentOrdersWidget::class,
        ];
    }

    public function getHeaderWidgetsColumns(): int | array
    {
        return [
            'default' => 1,
            'sm'      => 1,
            'lg'      => 1,
        ];
    }
}
