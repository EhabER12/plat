<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserActivity extends Model
{
    use HasFactory;

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'activity_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'activity_type',
        'description',
        'ip_address',
        'user_agent',
        'device',
        'browser',
        'os',
        'location',
        'entity_type',
        'entity_id',
        'properties',
        'occurred_at'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'properties' => 'array',
        'occurred_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Activity type constants
     */
    const TYPE_LOGIN = 'login';
    const TYPE_LOGOUT = 'logout';
    const TYPE_REGISTRATION = 'registration';
    const TYPE_PASSWORD_RESET = 'password_reset';
    const TYPE_PROFILE_UPDATE = 'profile_update';
    const TYPE_COURSE_VIEW = 'course_view';
    const TYPE_COURSE_ENROLL = 'course_enroll';
    const TYPE_LESSON_VIEW = 'lesson_view';
    const TYPE_LESSON_COMPLETE = 'lesson_complete';
    const TYPE_COURSE_COMPLETE = 'course_complete';
    const TYPE_QUIZ_START = 'quiz_start';
    const TYPE_QUIZ_COMPLETE = 'quiz_complete';
    const TYPE_ASSIGNMENT_SUBMIT = 'assignment_submit';
    const TYPE_PAYMENT = 'payment';
    const TYPE_REFUND_REQUEST = 'refund_request';
    const TYPE_REVIEW_SUBMIT = 'review_submit';
    const TYPE_CERTIFICATE_DOWNLOAD = 'certificate_download';
    const TYPE_FORUM_POST = 'forum_post';
    const TYPE_WISHLIST_ADD = 'wishlist_add';
    const TYPE_WISHLIST_REMOVE = 'wishlist_remove';
    const TYPE_CART_ADD = 'cart_add';
    const TYPE_CART_REMOVE = 'cart_remove';
    const TYPE_CART_CHECKOUT = 'cart_checkout';

    /**
     * Get the user who performed the activity.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    /**
     * Get the related entity.
     */
    public function entity()
    {
        if (!$this->entity_type || !$this->entity_id) {
            return null;
        }

        $entityClass = '\\App\\Models\\' . $this->entity_type;
        
        if (class_exists($entityClass)) {
            return $entityClass::find($this->entity_id);
        }
        
        return null;
    }

    /**
     * Log an activity for a user.
     *
     * @param int $userId
     * @param string $activityType
     * @param string $description
     * @param array $properties
     * @param string|null $entityType
     * @param int|null $entityId
     * @param string|null $ipAddress
     * @param string|null $userAgent
     * @return UserActivity
     */
    public static function log(
        $userId, 
        $activityType, 
        $description, 
        $properties = [], 
        $entityType = null, 
        $entityId = null, 
        $ipAddress = null, 
        $userAgent = null
    ) {
        $ipAddress = $ipAddress ?? request()->ip();
        $userAgent = $userAgent ?? request()->userAgent();
        
        $device = null;
        $browser = null;
        $os = null;
        $location = null;
        
        // Parse user agent if available
        if ($userAgent) {
            // Simple detection - in a real app, you might use a dedicated parser library
            $browser = self::detectBrowser($userAgent);
            $os = self::detectOS($userAgent);
            $device = self::detectDevice($userAgent);
        }
        
        // Create the activity record
        return self::create([
            'user_id' => $userId,
            'activity_type' => $activityType,
            'description' => $description,
            'ip_address' => $ipAddress,
            'user_agent' => $userAgent,
            'device' => $device,
            'browser' => $browser,
            'os' => $os,
            'location' => $location,
            'entity_type' => $entityType,
            'entity_id' => $entityId,
            'properties' => $properties,
            'occurred_at' => now()
        ]);
    }
    
    /**
     * Simple browser detection from user agent.
     *
     * @param string $userAgent
     * @return string|null
     */
    protected static function detectBrowser($userAgent)
    {
        $browsers = [
            'Chrome' => 'Chrome',
            'Firefox' => 'Firefox',
            'Safari' => 'Safari',
            'Edge' => 'Edge',
            'MSIE' => 'Internet Explorer',
            'Trident' => 'Internet Explorer',
            'Opera' => 'Opera'
        ];
        
        foreach ($browsers as $key => $browser) {
            if (stripos($userAgent, $key) !== false) {
                return $browser;
            }
        }
        
        return null;
    }
    
    /**
     * Simple OS detection from user agent.
     *
     * @param string $userAgent
     * @return string|null
     */
    protected static function detectOS($userAgent)
    {
        $os = [
            'Windows NT 10' => 'Windows 10',
            'Windows NT 6.3' => 'Windows 8.1',
            'Windows NT 6.2' => 'Windows 8',
            'Windows NT 6.1' => 'Windows 7',
            'Windows NT 6.0' => 'Windows Vista',
            'Windows NT 5.1' => 'Windows XP',
            'Mac OS X' => 'Mac OS X',
            'Linux' => 'Linux',
            'Android' => 'Android',
            'iPhone' => 'iOS',
            'iPad' => 'iOS'
        ];
        
        foreach ($os as $key => $value) {
            if (stripos($userAgent, $key) !== false) {
                return $value;
            }
        }
        
        return null;
    }
    
    /**
     * Simple device type detection from user agent.
     *
     * @param string $userAgent
     * @return string
     */
    protected static function detectDevice($userAgent)
    {
        $userAgent = strtolower($userAgent);
        
        if (strpos($userAgent, 'mobile') !== false) {
            if (strpos($userAgent, 'iphone') !== false || strpos($userAgent, 'ipod') !== false) {
                return 'iPhone';
            }
            
            if (strpos($userAgent, 'android') !== false) {
                return 'Android Phone';
            }
            
            return 'Mobile';
        }
        
        if (strpos($userAgent, 'tablet') !== false || strpos($userAgent, 'ipad') !== false) {
            return 'Tablet';
        }
        
        return 'Desktop';
    }

    /**
     * Scope a query to only include activities for a specific user.
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope a query to only include activities of a specific type.
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('activity_type', $type);
    }
    
    /**
     * Scope a query to only include activities related to a specific entity.
     */
    public function scopeForEntity($query, $entityType, $entityId = null)
    {
        $query = $query->where('entity_type', $entityType);
        
        if ($entityId !== null) {
            $query->where('entity_id', $entityId);
        }
        
        return $query;
    }

    /**
     * Scope a query to only include activities within a date range.
     */
    public function scopeInPeriod($query, $startDate, $endDate = null)
    {
        $query->where('occurred_at', '>=', $startDate);
        
        if ($endDate !== null) {
            $query->where('occurred_at', '<=', $endDate);
        }
        
        return $query;
    }

    /**
     * Scope a query to order by most recent first.
     */
    public function scopeLatest($query)
    {
        return $query->orderBy('occurred_at', 'desc');
    }
}
