<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $name
 * @property string|null $description
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\InventoryItem> $items
 * @property-read int|null $items_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InventoryCategory newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InventoryCategory newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InventoryCategory query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InventoryCategory whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InventoryCategory whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InventoryCategory whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InventoryCategory whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InventoryCategory whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class InventoryCategory extends Model
{
    protected $fillable = [
        'name',
        'description',
    ];

    public function items()
    {
        return $this->hasMany(InventoryItem::class, 'category_id');
    }
}
