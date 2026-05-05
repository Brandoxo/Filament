<?php

namespace App\Filament\Widgets;

use App\Models\Customer;
use App\Models\Order;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class BazarStatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $delivered = ['delivered', 'shipped', 'completed'];

        // ── Total de Ventas ──────────────────────────────────────────
        $totalVentas        = (float) Order::whereIn('status', $delivered)->sum('total_amount');
        $ventasEstaSemana   = (float) Order::whereIn('status', $delivered)->where('created_at', '>=', now()->startOfWeek())->sum('total_amount');
        $ventasSemanaAnterior = (float) Order::whereIn('status', $delivered)
            ->whereBetween('created_at', [now()->subWeek()->startOfWeek(), now()->subWeek()->endOfWeek()])
            ->sum('total_amount');
        $tendencia = $ventasSemanaAnterior > 0
            ? (int) round((($ventasEstaSemana - $ventasSemanaAnterior) / $ventasSemanaAnterior) * 100)
            : 0;

        // ── Prendas Vendidas (items JSON) ────────────────────────────
        try {
            $prendasVendidas = (int) Order::whereIn('status', $delivered)
                ->selectRaw('COALESCE(SUM(JSON_LENGTH(items_snapshot)), 0) as total')
                ->value('total');
        } catch (\Throwable) {
            $prendasVendidas = Order::whereIn('status', $delivered)->count();
        }

        // ── Clientes Nuevos ──────────────────────────────────────────
        $clientesNuevos      = Customer::where('created_at', '>=', now()->startOfMonth())->count();
        $clientesMesAnterior = Customer::whereBetween('created_at', [
            now()->subMonth()->startOfMonth(),
            now()->subMonth()->endOfMonth(),
        ])->count();

        // ── Sparklines (últimos 7 días) ──────────────────────────────
        $ventasChart   = collect(range(6, 0))->map(fn ($d) => (float) Order::whereIn('status', $delivered)->whereDate('created_at', now()->subDays($d))->sum('total_amount'))->toArray();
        $prendasChart  = collect(range(6, 0))->map(fn ($d) => Order::whereDate('created_at', now()->subDays($d))->count())->toArray();
        $clientesChart = collect(range(6, 0))->map(fn ($d) => Customer::whereDate('created_at', now()->subDays($d))->count())->toArray();

        return [
            Stat::make('Total de Ventas', '$' . number_format($totalVentas, 2, '.', ','))
                ->description($tendencia >= 0
                    ? "↑ {$tendencia}% vs. semana anterior"
                    : '↓ ' . abs($tendencia) . '% vs. semana anterior')
                ->descriptionIcon($tendencia >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($tendencia >= 0 ? 'success' : 'danger')
                ->chart($ventasChart),

            Stat::make('Prendas Vendidas', number_format($prendasVendidas))
                ->description('Artículos en órdenes completadas')
                ->descriptionIcon('heroicon-o-tag')
                ->color('warning')
                ->chart($prendasChart),

            Stat::make('Clientes Nuevos', $clientesNuevos)
                ->description("{$clientesMesAnterior} el mes pasado")
                ->descriptionIcon('heroicon-o-user-plus')
                ->color('primary')
                ->chart($clientesChart),
        ];
    }
}
