<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

class Event extends Model
{
    use HasFactory;

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'event_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'slug',
        'description',
        'content',
        'instructor_id',
        'thumbnail',
        'start_time',
        'end_time',
        'timezone',
        'location',
        'meeting_url',
        'meeting_id',
        'meeting_password',
        'platform', // zoom, google_meet, microsoft_teams, etc.
        'max_attendees',
        'is_recurring',
        'recurring_pattern',
        'price',
        'is_free',
        'status',
        'visibility',
        'registration_deadline',
        'category_id',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'language',
        'recording_url',
        'materials',
        'allow_recording',
        'reminder_sent',
        'is_featured',
        'allow_questions',
        'show_attendees',
        'certificate_available',
        'access_days'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'registration_deadline' => 'datetime',
        'max_attendees' => 'integer',
        'is_recurring' => 'boolean',
        'recurring_pattern' => 'array',
        'price' => 'decimal:2',
        'is_free' => 'boolean',
        'materials' => 'array',
        'allow_recording' => 'boolean',
        'reminder_sent' => 'boolean',
        'is_featured' => 'boolean',
        'allow_questions' => 'boolean',
        'show_attendees' => 'boolean',
        'certificate_available' => 'boolean',
        'access_days' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Status constants
     */
    const STATUS_DRAFT = 'draft';
    const STATUS_SCHEDULED = 'scheduled';
    const STATUS_LIVE = 'live';
    const STATUS_COMPLETED = 'completed';
    const STATUS_CANCELED = 'canceled';
    
    /**
     * Visibility constants
     */
    const VISIBILITY_PUBLIC = 'public';
    const VISIBILITY_PRIVATE = 'private';
    const VISIBILITY_COURSE_MEMBERS = 'course_members';
    const VISIBILITY_SUBSCRIBERS = 'subscribers';

    /**
     * Get the instructor of the event.
     */
    public function instructor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'instructor_id', 'user_id');
    }

    /**
     * Get the category of the event.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id', 'category_id');
    }

    /**
     * Get the registrations for the event.
     */
    public function registrations(): HasMany
    {
        return $this->hasMany(EventRegistration::class, 'event_id', 'event_id');
    }

    /**
     * Get the questions for the event.
     */
    public function questions(): HasMany
    {
        return $this->hasMany(EventQuestion::class, 'event_id', 'event_id');
    }

    /**
     * Get the tags for the event.
     */
    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'event_tag', 'event_id', 'tag_id');
    }

    /**
     * Get the courses this event is associated with.
     */
    public function courses(): BelongsToMany
    {
        return $this->belongsToMany(Course::class, 'course_event', 'event_id', 'course_id')
                    ->withTimestamps();
    }

    /**
     * Get the registered users for the event.
     */
    public function attendees(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'event_registrations', 'event_id', 'user_id')
                    ->withPivot(['status', 'attended', 'registration_time', 'attendance_time'])
                    ->withTimestamps();
    }

    /**
     * Get the thumbnail URL.
     *
     * @return string|null
     */
    public function getThumbnailUrlAttribute()
    {
        if (!$this->thumbnail) {
            return null;
        }
        
        // Check if the thumbnail is a media library ID
        $media = MediaLibrary::find($this->thumbnail);
        if ($media) {
            return $media->url;
        }
        
        // If it's a URL already, return it
        if (filter_var($this->thumbnail, FILTER_VALIDATE_URL)) {
            return $this->thumbnail;
        }
        
        // Otherwise assume it's a path in the storage
        return asset('storage/' . $this->thumbnail);
    }

    /**
     * Get the duration of the event in minutes.
     *
     * @return int
     */
    public function getDurationAttribute()
    {
        if (!$this->start_time || !$this->end_time) {
            return 0;
        }
        
        return $this->start_time->diffInMinutes($this->end_time);
    }

    /**
     * Check if the event is free.
     *
     * @return bool
     */
    public function isFree()
    {
        return $this->is_free || $this->price == 0;
    }

    /**
     * Check if registration is still open.
     *
     * @return bool
     */
    public function isRegistrationOpen()
    {
        if ($this->status !== self::STATUS_SCHEDULED) {
            return false;
        }
        
        if (!$this->registration_deadline) {
            return true;
        }
        
        return now()->lt($this->registration_deadline);
    }

    /**
     * Check if the event is full.
     *
     * @return bool
     */
    public function isFull()
    {
        if (!$this->max_attendees) {
            return false;
        }
        
        return $this->registrations()->where('status', 'confirmed')->count() >= $this->max_attendees;
    }

    /**
     * Get available seats.
     *
     * @return int|null
     */
    public function getAvailableSeatsAttribute()
    {
        if (!$this->max_attendees) {
            return null; // Unlimited
        }
        
        $registered = $this->registrations()->where('status', 'confirmed')->count();
        
        return max(0, $this->max_attendees - $registered);
    }

    /**
     * Record attendance for a user.
     *
     * @param int $userId
     * @return bool
     */
    public function recordAttendance($userId)
    {
        $registration = $this->registrations()
                             ->where('user_id', $userId)
                             ->where('status', 'confirmed')
                             ->first();
        
        if (!$registration) {
            return false;
        }
        
        $registration->update([
            'attended' => true,
            'attendance_time' => now()
        ]);
        
        return true;
    }

    /**
     * Register a user for the event.
     *
     * @param int $userId
     * @param array $data
     * @return EventRegistration|null
     */
    public function registerUser($userId, array $data = [])
    {
        if (!$this->isRegistrationOpen() || $this->isFull()) {
            return null;
        }
        
        $existingRegistration = $this->registrations()
                                      ->where('user_id', $userId)
                                      ->first();
        
        if ($existingRegistration) {
            return $existingRegistration;
        }
        
        return $this->registrations()->create(array_merge([
            'user_id' => $userId,
            'status' => 'confirmed',
            'registration_time' => now()
        ], $data));
    }

    /**
     * Cancel registration for a user.
     *
     * @param int $userId
     * @return bool
     */
    public function cancelRegistration($userId)
    {
        $registration = $this->registrations()
                             ->where('user_id', $userId)
                             ->first();
        
        if (!$registration) {
            return false;
        }
        
        $registration->update(['status' => 'canceled']);
        
        return true;
    }

    /**
     * Mark the event as started.
     *
     * @return $this
     */
    public function markAsStarted()
    {
        $this->update(['status' => self::STATUS_LIVE]);
        
        return $this;
    }

    /**
     * Mark the event as completed.
     *
     * @param string|null $recordingUrl
     * @return $this
     */
    public function markAsCompleted($recordingUrl = null)
    {
        $data = ['status' => self::STATUS_COMPLETED];
        
        if ($recordingUrl) {
            $data['recording_url'] = $recordingUrl;
        }
        
        $this->update($data);
        
        return $this;
    }

    /**
     * Mark the event as canceled.
     *
     * @return $this
     */
    public function markAsCanceled()
    {
        $this->update(['status' => self::STATUS_CANCELED]);
        
        // Notify registered users
        foreach ($this->registrations as $registration) {
            // Placeholder for notification logic
            // $registration->user->notify(new EventCanceledNotification($this));
        }
        
        return $this;
    }

    /**
     * Schedule a reminder for this event.
     *
     * @param int $hoursBeforeEvent
     * @return void
     */
    public function scheduleReminder($hoursBeforeEvent = 24)
    {
        $reminderTime = $this->start_time->subHours($hoursBeforeEvent);
        
        // This is a placeholder for scheduling logic
        // In a real app, you'd use Laravel's scheduler or queue
        // For example: 
        // SendEventReminderJob::dispatch($this->event_id)->delay($reminderTime);
    }

    /**
     * Send reminders to all registered users.
     *
     * @return int Number of reminders sent
     */
    public function sendReminders()
    {
        if ($this->reminder_sent) {
            return 0;
        }
        
        $count = 0;
        foreach ($this->registrations()->where('status', 'confirmed')->get() as $registration) {
            // Placeholder for notification logic
            // $registration->user->notify(new EventReminderNotification($this));
            $count++;
        }
        
        $this->update(['reminder_sent' => true]);
        
        return $count;
    }

    /**
     * Generate event certificate for a user.
     *
     * @param int $userId
     * @return string|null Certificate URL or null if not available
     */
    public function generateCertificate($userId)
    {
        if (!$this->certificate_available) {
            return null;
        }
        
        $registration = $this->registrations()
                             ->where('user_id', $userId)
                             ->where('attended', true)
                             ->first();
        
        if (!$registration) {
            return null;
        }
        
        // Placeholder for certificate generation logic
        // In a real implementation, this would:
        // 1. Generate a PDF certificate
        // 2. Store it in the system
        // 3. Return the URL
        
        // For now, return a placeholder URL
        return route('certificates.view', ['event' => $this->event_id, 'user' => $userId]);
    }

    /**
     * Calculate total registrations.
     *
     * @return int
     */
    public function getRegistrationsCountAttribute()
    {
        return $this->registrations()->where('status', 'confirmed')->count();
    }

    /**
     * Calculate attendance percentage.
     *
     * @return float
     */
    public function getAttendanceRateAttribute()
    {
        $registrations = $this->registrations()->where('status', 'confirmed')->count();
        
        if ($registrations === 0) {
            return 0;
        }
        
        $attended = $this->registrations()->where('status', 'confirmed')->where('attended', true)->count();
        
        return ($attended / $registrations) * 100;
    }

    /**
     * Scope a query to only include upcoming events.
     */
    public function scopeUpcoming($query)
    {
        return $query->where('start_time', '>', now())
                    ->where('status', self::STATUS_SCHEDULED);
    }

    /**
     * Scope a query to only include past events.
     */
    public function scopePast($query)
    {
        return $query->where('end_time', '<', now());
    }

    /**
     * Scope a query to only include events happening today.
     */
    public function scopeToday($query)
    {
        return $query->whereDate('start_time', Carbon::today());
    }

    /**
     * Scope a query to only include events that are currently live.
     */
    public function scopeLive($query)
    {
        return $query->where('status', self::STATUS_LIVE);
    }

    /**
     * Scope a query to only include completed events.
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', self::STATUS_COMPLETED);
    }

    /**
     * Scope a query to only include canceled events.
     */
    public function scopeCanceled($query)
    {
        return $query->where('status', self::STATUS_CANCELED);
    }

    /**
     * Scope a query to only include public events.
     */
    public function scopePublic($query)
    {
        return $query->where('visibility', self::VISIBILITY_PUBLIC);
    }

    /**
     * Scope a query to only include featured events.
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * Scope a query to only include free events.
     */
    public function scopeFree($query)
    {
        return $query->where('is_free', true)
                    ->orWhere('price', 0);
    }

    /**
     * Scope a query to only include paid events.
     */
    public function scopePaid($query)
    {
        return $query->where('is_free', false)
                    ->where('price', '>', 0);
    }

    /**
     * Scope a query to only include events by a specific instructor.
     */
    public function scopeByInstructor($query, $instructorId)
    {
        return $query->where('instructor_id', $instructorId);
    }

    /**
     * Scope a query to only include events in a specific category.
     */
    public function scopeInCategory($query, $categoryId)
    {
        return $query->where('category_id', $categoryId);
    }

    /**
     * Scope a query to only include events with available spots.
     */
    public function scopeWithAvailableSpots($query)
    {
        return $query->where(function($q) {
            $q->whereNull('max_attendees')
              ->orWhereRaw('max_attendees > (SELECT COUNT(*) FROM event_registrations WHERE event_registrations.event_id = events.event_id AND status = "confirmed")');
        });
    }

    /**
     * Scope a query to only include events in a specific time range.
     */
    public function scopeInTimeRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('start_time', [$startDate, $endDate]);
    }
}
