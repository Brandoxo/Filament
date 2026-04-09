<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Look extends Model
{
    protected $fillable = [
        'title',
        'description',
        'is_active'
    ];
}
