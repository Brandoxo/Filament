<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use App\Services\OrderStatusService;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;

class RecentOrdersWidget extends BaseWidget
{
    protected static ?int $sort = 4;

    protected int | string | array $columnSpan = 'full';

    protected static ?string $heading = 'Todas las Órdenes';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Order::query()
                    ->with(['customer', 'shippingRate.shippingCarrier', 'coupon'])
                    ->latest()
            )
            ->columns([
                TextColumn::make('order_number')
                    ->label('# Orden')
                    ->sortable()
                    ->searchable()
                    ->copyable()
                    ->weight('bold'),

                TextColumn::make('customer.name')
                    ->label('Cliente')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('customer.email')
                    ->label('Email')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('total_amount')
                    ->label('Total')
                    ->money('MXN')
                    ->sortable(),

                TextColumn::make('payment_method')
                    ->label('Método de Pago')
                    ->formatStateUsing(fn (?string $state): string => Order::PAYMENT_METHODS[$state] ?? ($state ?? '—'))
                    ->badge()
                    ->color('gray')
                    ->toggleable(),

                TextColumn::make('status')
                    ->label('Estado')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => Order::STATUSES[$state] ?? ucfirst($state))
                    ->color(fn (string $state): string => Order::STATUS_COLORS[$state] ?? 'gray')
                    ->sortable(),

                TextColumn::make('delivery_mode')
                    ->label('Entrega')
                    ->formatStateUsing(fn (?string $state): string => Order::DELIVERY_MODES[$state] ?? ($state ?? '—'))
                    ->badge()
                    ->color(fn (?string $state): string => $state === 'personal' ? 'warning' : 'gray')
                    ->toggleable(),

                TextColumn::make('shippingRate.shippingCarrier.name')
                    ->label('Paquetería')
                    ->placeholder('—')
                    ->toggleable(),

                TextColumn::make('shippingRate.name')
                    ->label('Tarifa')
                    ->placeholder('—')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('tracking_number')
                    ->label('Guía')
                    ->searchable()
                    ->copyable()
                    ->placeholder('—')
                    ->toggleable(),

                TextColumn::make('coupon.code')
                    ->label('Cupón')
                    ->badge()
                    ->color('info')
                    ->placeholder('—')
                    ->toggleable(),

                TextColumn::make('discount_amount')
                    ->label('Descuento')
                    ->money('MXN')
                    ->placeholder('—')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('created_at')
                    ->label('Fecha de Compra')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),

                TextColumn::make('status_changed_at')
                    ->label('Último Cambio')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->placeholder('—')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Estado')
                    ->options(Order::STATUSES),

                SelectFilter::make('payment_method')
                    ->label('Método de Pago')
                    ->options(Order::PAYMENT_METHODS),

                SelectFilter::make('delivery_mode')
                    ->label('Modo de Entrega')
                    ->options(Order::DELIVERY_MODES),

                Filter::make('date_range')
                    ->label('Rango de Fechas')
                    ->form([
                        DatePicker::make('from')->label('Desde'),
                        DatePicker::make('until')->label('Hasta'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when($data['from'],  fn ($q) => $q->whereDate('created_at', '>=', $data['from']))
                            ->when($data['until'], fn ($q) => $q->whereDate('created_at', '<=', $data['until']));
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        if ($data['from'])  $indicators[] = 'Desde: ' . $data['from'];
                        if ($data['until']) $indicators[] = 'Hasta: ' . $data['until'];
                        return $indicators;
                    }),
            ])
            ->actions([
                Action::make('change_status')
                    ->label('Cambiar Estado')
                    ->icon('heroicon-o-arrow-path')
                    ->color('gray')
                    ->form([
                        Select::make('new_status')
                            ->label('Nuevo Estado')
                            ->options(Order::STATUSES)
                            ->required()
                            ->placeholder('Selecciona el estado destino'),
                    ])
                    ->action(function (Order $record, array $data): void {
                        try {
                            app(OrderStatusService::class)->transition($record, $data['new_status']);
                            $label = Order::STATUSES[$data['new_status']] ?? $data['new_status'];
                            Notification::make()
                                ->success()
                                ->title('Estado actualizado')
                                ->body("Orden #{$record->order_number} → «{$label}»")
                                ->send();
                        } catch (\Throwable $e) {
                            Notification::make()
                                ->danger()
                                ->title('Transición no permitida')
                                ->body($e->getMessage())
                                ->send();
                        }
                    })
                    ->visible(fn (Order $record): bool => ! empty(Order::TRANSITIONS[$record->status])),
            ])
            ->defaultPaginationPageOption(10)
            ->paginated([10, 25, 50, 100])
            ->striped()
            ->defaultSort('created_at', 'desc');
    }
}
