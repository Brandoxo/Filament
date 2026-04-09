<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'title',
        'slug',
        'description',
        'price',
        'original_price',
        'stock',
        'brand',
        'size',
        'category_id',
        'image_url',
        'image_url_2',
        'image_url_3',
        'image_url_4',
        'is_new',
        'currency',
        'variant_options',
        'is_active',
    ];

    protected $guarded = [];

    protected $casts = [
        'variant_options' => 'array',
        'base_price' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    protected $appends = ['images'];

    // Accessor para obtener todas las imágenes en un array
    public function getImagesAttribute()
    {
        $images = [];
        
        // Recopilar todas las URLs de imágenes que no sean nulas
        if ($this->image_url) {
            $images[] = $this->image_url;
        }
        if ($this->image_url_2) {
            $images[] = $this->image_url_2;
        }
        if ($this->image_url_3) {
            $images[] = $this->image_url_3;
        }
        if ($this->image_url_4) {
            $images[] = $this->image_url_4;
        }
        
        return $images;
    }



}
