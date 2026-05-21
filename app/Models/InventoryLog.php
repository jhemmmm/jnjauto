<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $item_id
 * @property int|null $user_id
 * @property string $type
 * @property int $quantity
 * @property int $quantity_before
 * @property int $quantity_after
 * @property string|null $notes
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\InventoryItem $item
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InventoryLog newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InventoryLog newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InventoryLog query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InventoryLog whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InventoryLog whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InventoryLog whereItemId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InventoryLog whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InventoryLog whereQuantity($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InventoryLog whereQuantityAfter($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InventoryLog whereQuantityBefore($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InventoryLog whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InventoryLog whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InventoryLog whereUserId($value)
 * @mixin \Eloquent
 */
class InventoryLog extends Model
{
    protected $fillable = [
        'item_id',
        'user_id',
        'type',
        'quantity',
        'quantity_before',
        'quantity_after',
        'notes',
        'reference_type',
        'reference_id',
    ];

    public function item()
    {
        return $this->belongsTo(InventoryItem::class, 'item_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
