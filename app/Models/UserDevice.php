<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserDevice extends Model
{
    use HasFactory;

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'device_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'device_type',
        'device_name',
        'browser',
        'browser_version',
        'operating_system',
        'ip_address',
        'last_login_at',
        'fcm_token',
        'is_trusted'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'last_login_at' => 'datetime',
        'is_trusted' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Get the user who owns the device.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    /**
     * Update the last login information.
     */
    public function updateLastLogin($ipAddress = null)
    {
        $data = ['last_login_at' => now()];
        
        if ($ipAddress) {
            $data['ip_address'] = $ipAddress;
        }
        
        $this->update($data);
        return $this;
    }

    /**
     * Mark the device as trusted.
     */
    public function markAsTrusted()
    {
        $this->update(['is_trusted' => true]);
        return $this;
    }

    /**
     * Mark the device as untrusted.
     */
    public function markAsUntrusted()
    {
        $this->update(['is_trusted' => false]);
        return $this;
    }

    /**
     * Update the FCM token for push notifications.
     */
    public function updateFcmToken($token)
    {
        $this->update(['fcm_token' => $token]);
        return $this;
    }

    /**
     * Scope a query to only include trusted devices.
     */
    public function scopeTrusted($query)
    {
        return $query->where('is_trusted', true);
    }

    /**
     * Scope a query to only include devices for a specific user.
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope a query to only include recently active devices.
     */
    public function scopeRecentlyActive($query, $days = 30)
    {
        return $query->where('last_login_at', '>=', now()->subDays($days));
    }
}
