<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
        'image_url',
        'col_span'
    ];

    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
