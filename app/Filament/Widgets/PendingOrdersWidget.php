<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use App\Services\OrderStatusService;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Support\Collection;

class PendingOrdersWidget extends BaseWidget
{
    protected static ?int $sort = 3;

    protected int | string | array $columnSpan = 'full';

    protected static ?string $heading = 'Órdenes Pendientes de Gestión';

    protected static string $pollingInterval = '30s';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Order::query()
                    ->where('status', 'pending')
                    ->with('customer')
                    ->oldest()
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
                    ->label('Pago')
                    ->formatStateUsing(fn (?string $state): string => Order::PAYMENT_METHODS[$state] ?? ($state ?? '—'))
                    ->badge()
                    ->color('gray'),

                TextColumn::make('status')
                    ->label('Estado')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => Order::STATUSES[$state] ?? ucfirst($state))
                    ->color(fn (string $state): string => Order::STATUS_COLORS[$state] ?? 'gray'),

                TextColumn::make('created_at')
                    ->label('Recibida')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->since(),
            ])
            ->actions([
                Action::make('advance')
                    ->label('Procesar')
                    ->icon('heroicon-o-arrow-right-circle')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->modalHeading('¿Marcar como En Proceso?')
                    ->modalDescription(fn (Order $record): string => "La orden #{$record->order_number} pasará a «En proceso».")
                    ->action(function (Order $record): void {
                        try {
                            app(OrderStatusService::class)->transition($record, 'processing');
                            Notification::make()
                                ->success()
                                ->title('Orden en proceso')
                                ->body("#{$record->order_number} fue marcada como «En proceso».")
                                ->send();
                        } catch (\Throwable $e) {
                            Notification::make()
                                ->danger()
                                ->title('No se pudo actualizar')
                                ->body($e->getMessage())
                                ->send();
                        }
                    }),

                Action::make('cancel')
                    ->label('Cancelar')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('¿Cancelar esta orden?')
                    ->modalDescription(fn (Order $record): string => "La orden #{$record->order_number} será cancelada. Esta acción es permanente.")
                    ->action(function (Order $record): void {
                        try {
                            app(OrderStatusService::class)->transition($record, 'cancelled');
                            Notification::make()
                                ->warning()
                                ->title('Orden cancelada')
                                ->body("#{$record->order_number} fue cancelada.")
                                ->send();
                        } catch (\Throwable $e) {
                            Notification::make()
                                ->danger()
                                ->title('No se pudo cancelar')
                                ->body($e->getMessage())
                                ->send();
                        }
                    }),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    BulkAction::make('bulk_process')
                        ->label('Marcar como En Proceso')
                        ->icon('heroicon-o-arrow-path')
                        ->color('warning')
                        ->requiresConfirmation()
                        ->modalHeading('¿Procesar las órdenes seleccionadas?')
                        ->modalDescription(fn (Collection $records): string => "Se procesarán {$records->count()} orden(es). Sólo se actualizarán las que permitan la transición.")
                        ->action(function (Collection $records): void {
                            $result = app(OrderStatusService::class)->bulkTransition($records, 'processing');
                            $this->sendBulkNotification($result, 'processing');
                        })
                        ->deselectRecordsAfterCompletion(),

                    BulkAction::make('bulk_cancel')
                        ->label('Cancelar Seleccionadas')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->modalHeading('¿Cancelar las órdenes seleccionadas?')
                        ->modalDescription(fn (Collection $records): string => "Se cancelarán {$records->count()} orden(es). Esta acción es irreversible.")
                        ->action(function (Collection $records): void {
                            $result = app(OrderStatusService::class)->bulkTransition($records, 'cancelled');
                            $this->sendBulkNotification($result, 'cancelled');
                        })
                        ->deselectRecordsAfterCompletion(),
                ]),
            ])
            ->emptyStateHeading('Sin órdenes pendientes')
            ->emptyStateDescription('Todas las órdenes han sido gestionadas. ¡Buen trabajo!')
            ->emptyStateIcon('heroicon-o-check-circle')
            ->striped()
            ->defaultSort('created_at', 'asc');
    }

    private function sendBulkNotification(array $result, string $status): void
    {
        $label = Order::STATUSES[$status] ?? $status;

        if ($result['updated'] > 0) {
            Notification::make()
                ->success()
                ->title("{$result['updated']} orden(es) → «{$label}»")
                ->send();
        }

        if ($result['skipped'] > 0) {
            Notification::make()
                ->warning()
                ->title("{$result['skipped']} orden(es) omitidas")
                ->body(implode("\n", array_slice($result['errors'], 0, 5)))
                ->persistent()
                ->send();
        }
    }
}
