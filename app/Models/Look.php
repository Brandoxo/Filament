<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Look extends Model
{
    protected $fillable = [
        'title',
        'description',
        'price',
        'is_active'
    ];

    public function products()
    {
        return $this->belongsToMany(Product::class)->withPivot('quantity')->withTimestamps();
    }
}
