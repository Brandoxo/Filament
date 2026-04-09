<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductVariant extends Model
{
    protected $guarded = [];

    protected $casts = [
        'attributes' => 'array',
        'price' => 'decimal:2',
    ];
}
