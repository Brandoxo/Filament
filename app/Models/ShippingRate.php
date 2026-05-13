<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ShippingRate extends Model
{
    protected $guarded = [];

    protected $casts = [
        'price'      => 'decimal:2',
        'free_from'  => 'decimal:2',
        'is_active'  => 'boolean',
    ];

    public function shippingCarrier(): BelongsTo
    {
        return $this->belongsTo(ShippingCarrier::class);
    }

    public function isFreeFor(float $orderAmount): bool
    {
        return $this->free_from !== null && $orderAmount >= $this->free_from;
    }

    public function effectivePrice(float $orderAmount): float
    {
        return $this->isFreeFor($orderAmount) ? 0.0 : (float) $this->price;
    }
}
