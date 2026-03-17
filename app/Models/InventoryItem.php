<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
