<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserAddress extends Model
{
    use SoftDeletes;
    protected $table = 'user_address';

    protected $fillable = [
        'user_id',
        'name',
        'street',
        'number_int',
        'number_ext',
        'city',
        'state',
        'postal_code',
        'country',
        'reference',
        'is_default',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
