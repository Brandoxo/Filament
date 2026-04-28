<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use Filament\Widgets\ChartWidget;

class OrdersChartWidget extends ChartWidget
{
    protected ?string $heading = 'Órdenes — Últimos 7 días';

    protected static ?int $sort = 2;

    protected ?string $maxHeight = '280px';

    protected int | string | array $columnSpan = 'full';

    protected function getData(): array
    {
        $labels = [];
        $counts = [];

        for ($i = 6; $i >= 0; $i--) {
            $date     = now()->subDays($i);
            $labels[] = $date->format('D d/m');
            $counts[] = Order::whereDate('created_at', $date)->count();
        }

        return [
            'datasets' => [
                [
                    'label'                => 'Órdenes',
                    'data'                 => $counts,
                    'borderColor'          => '#c05621',
                    'backgroundColor'      => 'rgba(192, 86, 33, 0.07)',
                    'tension'              => 0.4,
                    'fill'                 => true,
                    'pointBackgroundColor' => '#b79857',
                    'pointBorderColor'     => '#2a2826',
                    'pointBorderWidth'     => 2,
                    'pointRadius'          => 5,
                    'pointHoverRadius'     => 8,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => ['display' => false],
                'tooltip' => [
                    'callbacks' => [],
                ],
            ],
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'ticks'       => ['stepSize' => 1, 'precision' => 0],
                    'grid'        => ['color' => 'rgba(183, 152, 87, 0.1)'],
                ],
                'x' => [
                    'grid' => ['display' => false],
                ],
            ],
        ];
    }
}
