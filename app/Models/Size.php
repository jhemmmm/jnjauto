<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $name
 * @property string|null $description
 * @property numeric $multiplier
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Size newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Size newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Size query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Size whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Size whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Size whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Size whereMultiplier($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Size whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Size whereUpdatedAt($value)
 * @mixin \Eloquent
 */
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
