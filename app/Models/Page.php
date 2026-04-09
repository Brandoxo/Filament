<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    protected $guarded = [];

    protected $casts = [
        'content_structure' => 'array',
        'is_published' => 'boolean'
    ];
}
