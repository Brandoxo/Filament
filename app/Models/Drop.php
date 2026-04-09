<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Drop extends Model
{
    protected $fillable = [
        'title',
        'launch_date',
        'description',
        'status'
    ];
}
