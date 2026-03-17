<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Size extends Model
{
    protected $fillable = [
        'name',
        'description',
        'multiplier',
    ];

    protected $casts = [
        'multiplier' => 'decimal:2',
    ];
}
