<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ShippingCarrier extends Model
{
    protected $guarded = [];

    public function shippingRates(): HasMany
    {
        return $this->hasMany(ShippingRate::class)->orderBy('sort_order');
    }

    public function activeRates(): HasMany
    {
        return $this->hasMany(ShippingRate::class)->where('is_active', true)->orderBy('sort_order');
    }
}
