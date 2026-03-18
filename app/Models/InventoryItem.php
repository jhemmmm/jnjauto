<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $category_id
 * @property string $name
 * @property string|null $description
 * @property string|null $sku
 * @property string $unit
 * @property numeric $cost
 * @property int $quantity
 * @property int $reorder_level
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\InventoryCategory $category
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\InventoryLog> $logs
 * @property-read int|null $logs_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InventoryItem newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InventoryItem newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InventoryItem query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InventoryItem whereCategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InventoryItem whereCost($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InventoryItem whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InventoryItem whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InventoryItem whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InventoryItem whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InventoryItem whereQuantity($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InventoryItem whereReorderLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InventoryItem whereSku($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InventoryItem whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InventoryItem whereUnit($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InventoryItem whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class InventoryItem extends Model
{
    protected $fillable = [
        'category_id',
        'name',
        'description',
        'sku',
        'unit',
        'cost',
        'quantity',
        'reorder_level',
        'status',
    ];

    protected $casts = [
        'cost' => 'decimal:2',
    ];

    public function category()
    {
        return $this->belongsTo(InventoryCategory::class, 'category_id');
    }

    public function logs()
    {
        return $this->hasMany(InventoryLog::class, 'item_id');
    }

    /**
     * Recalculate status based on current quantity vs reorder level.
     */
    public function refreshStatus(): void
    {
        if ($this->quantity <= 0) {
            $this->status = 'out_of_stock';
        } elseif ($this->quantity <= $this->reorder_level) {
            $this->status = 'low_stock';
        } else {
            $this->status = 'in_stock';
        }
        $this->saveQuietly();
    }
}
