<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Order extends Model
{
    protected $guarded = [];

    protected $casts = [
        'shipping_address_snapshot' => 'array',
        'items_snapshot'            => 'array',
        'carrier_snapshot'          => 'array',
        'coupon_snapshot'           => 'array',
        'total_amount'              => 'decimal:2',
        'discount_amount'           => 'decimal:2',
        'status_changed_at'         => 'datetime',
    ];

    const STATUSES = [
        'pending'    => 'Pendiente',
        'processing' => 'En proceso',
        'paid'       => 'Pagado',
        'shipped'    => 'Enviado',
        'completed'  => 'Completado',
        'cancelled'  => 'Cancelado',
        'returned'   => 'Devuelto',
    ];

    const STATUS_COLORS = [
        'pending'    => 'warning',
        'processing' => 'info',
        'paid'       => 'success',
        'shipped'    => 'primary',
        'completed'  => 'success',
        'cancelled'  => 'danger',
        'returned'   => 'gray',
    ];

    // Allowed forward transitions for each status
    const TRANSITIONS = [
        'pending'    => ['processing', 'cancelled'],
        'processing' => ['paid', 'cancelled'],
        'paid'       => ['shipped', 'cancelled'],
        'shipped'    => ['completed', 'returned'],
        'completed'  => ['returned'],
        'cancelled'  => [],
        'returned'   => [],
    ];

    const PAYMENT_METHODS = [
        'card'        => 'Tarjeta',
        'transfer'    => 'Transferencia',
        'cash'        => 'Efectivo',
        'paypal'      => 'PayPal',
        'mercadopago' => 'MercadoPago',
        'other'       => 'Otro',
    ];

    const DELIVERY_MODES = [
        'parcel'   => 'Paquetería',
        'personal' => 'Entrega personal',
    ];

    public function canTransitionTo(string $newStatus): bool
    {
        return in_array($newStatus, self::TRANSITIONS[$this->status] ?? [], true);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function shop(): BelongsTo
    {
        return $this->belongsTo(Shop::class);
    }

    public function shippingRate(): BelongsTo
    {
        return $this->belongsTo(ShippingRate::class);
    }

    public function coupon(): BelongsTo
    {
        return $this->belongsTo(Coupon::class);
    }
}
