<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $fillable = [
        'user_id',
        'type',
        'title',
        'message',
        'icon',
        'icon_color',
        'link',
        'data',
        'read_at',
    ];

    protected $casts = [
        'data' => 'array',
        'read_at' => 'datetime',
    ];

    /* ── Relationships ── */

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /* ── Scopes ── */

    public function scopeUnread($query)
    {
        return $query->whereNull('read_at');
    }

    public function scopeRead($query)
    {
        return $query->whereNotNull('read_at');
    }

    /* ── Helpers ── */

    public function markAsRead()
    {
        if (is_null($this->read_at)) {
            $this->update(['read_at' => now()]);
        }
    }

    /**
     * Create a notification for all admin users (role_id = 2).
     */
    public static function notifyAdmins(string $type, string $title, string $message, ?string $icon = null, ?string $iconColor = null, ?string $link = null, ?array $data = null): void
    {
        $admins = User::where('role_id', 2)->get();

        foreach ($admins as $admin) {
            static::create([
                'user_id'    => $admin->id,
                'type'       => $type,
                'title'      => $title,
                'message'    => $message,
                'icon'       => $icon,
                'icon_color' => $iconColor,
                'link'       => $link,
                'data'       => $data,
            ]);
        }
    }
}
