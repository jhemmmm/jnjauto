<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    //
    protected $fillable = [
        'date',
        'time',
        'service_id',
        'size_id',
        'customer_name',
        'customer_email',
        'customer_phone',
        'notes',
        'status',
        'amount',
        'completed_at',
    ];

    protected $casts = [
        'date' => 'date',
        'completed_at' => 'datetime',
        'amount' => 'decimal:2',
    ];

    /**
     * Scope: only completed appointments (sales).
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function getTimeAttribute($value)
    {
        if (empty($value)) {
            return $value;
        }

        try {
            return \Carbon\Carbon::createFromFormat('H:i:s', $value)->format('H:i');
        } catch (\Exception $e) {
            // Already in H:i format or another format
            return $value;
        }
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function size()
    {
        return $this->belongsTo(Size::class);
    }
}
