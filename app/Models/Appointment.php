<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property \Illuminate\Support\Carbon $date
 * @property string $time
 * @property int $service_id
 * @property int $size_id
 * @property string $customer_name
 * @property string|null $customer_email
 * @property string|null $customer_phone
 * @property string|null $notes
 * @property string $status
 * @property numeric|null $amount
 * @property \Illuminate\Support\Carbon|null $completed_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Service|null $service
 * @property-read \App\Models\Size|null $size
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Appointment completed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Appointment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Appointment newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Appointment query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Appointment whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Appointment whereCompletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Appointment whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Appointment whereCustomerEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Appointment whereCustomerName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Appointment whereCustomerPhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Appointment whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Appointment whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Appointment whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Appointment whereServiceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Appointment whereSizeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Appointment whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Appointment whereTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Appointment whereUpdatedAt($value)
 * @mixin \Eloquent
 */
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
