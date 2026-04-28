<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\BazarStatsOverview;
use App\Filament\Widgets\OrdersChartWidget;
use Filament\Pages\Dashboard as BaseDashboard;
use Illuminate\Contracts\View\View;

class Dashboard extends BaseDashboard
{
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-home';

    protected static ?string $navigationLabel = 'Inicio';

    protected static ?string $title = 'Dashboard';

    public function getHeader(): ?View
    {
        return view('components.bazar-welcome-banner');
    }

    public function getHeaderWidgets(): array
    {
        return [
            BazarStatsOverview::class,
            OrdersChartWidget::class,
        ];
    }

    public function getHeaderWidgetsColumns(): int | array
    {
        return [
            'default' => 1,
            'sm'      => 1,
            'lg'      => 3,
        ];
    }
}
