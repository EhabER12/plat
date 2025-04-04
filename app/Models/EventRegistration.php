<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EventRegistration extends Model
{
    use HasFactory;

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'registration_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'event_id',
        'user_id',
        'status',
        'payment_id',
        'amount_paid',
        'registered_by',
        'registration_time',
        'attended',
        'attendance_time',
        'certificate_issued',
        'certificate_url',
        'check_in_code',
        'notes',
        'custom_fields',
        'join_url',
        'cancellation_reason',
        'canceled_at',
        'reminder_sent',
        'feedback_submitted'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'amount_paid' => 'decimal:2',
        'registration_time' => 'datetime',
        'attended' => 'boolean',
        'attendance_time' => 'datetime',
        'certificate_issued' => 'boolean',
        'custom_fields' => 'array',
        'canceled_at' => 'datetime',
        'reminder_sent' => 'boolean',
        'feedback_submitted' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Status constants
     */
    const STATUS_PENDING = 'pending';
    const STATUS_CONFIRMED = 'confirmed';
    const STATUS_WAITLISTED = 'waitlisted';
    const STATUS_CANCELED = 'canceled';
    const STATUS_REJECTED = 'rejected';

    /**
     * Get the event this registration belongs to.
     */
    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class, 'event_id', 'event_id');
    }

    /**
     * Get the user who registered for the event.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    /**
     * Get the admin user who registered the user (if applicable).
     */
    public function registeredBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'registered_by', 'user_id');
    }

    /**
     * Get the payment record for this registration.
     */
    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class, 'payment_id', 'payment_id');
    }

    /**
     * Mark the registration as confirmed.
     *
     * @return $this
     */
    public function confirm()
    {
        $this->update(['status' => self::STATUS_CONFIRMED]);
        
        return $this;
    }

    /**
     * Mark the registration as waitlisted.
     *
     * @return $this
     */
    public function waitlist()
    {
        $this->update(['status' => self::STATUS_WAITLISTED]);
        
        return $this;
    }

    /**
     * Mark the user as attended.
     *
     * @return $this
     */
    public function markAttended()
    {
        $this->update([
            'attended' => true,
            'attendance_time' => now()
        ]);
        
        return $this;
    }

    /**
     * Cancel the registration.
     *
     * @param string|null $reason
     * @return $this
     */
    public function cancel($reason = null)
    {
        $this->update([
            'status' => self::STATUS_CANCELED,
            'cancellation_reason' => $reason,
            'canceled_at' => now()
        ]);
        
        return $this;
    }

    /**
     * Generate a unique check-in code for this registration.
     *
     * @return string
     */
    public function generateCheckInCode()
    {
        $code = strtoupper(substr(md5($this->registration_id . $this->user_id . time()), 0, 8));
        $this->update(['check_in_code' => $code]);
        
        return $code;
    }

    /**
     * Generate certificate for the attendee.
     *
     * @return string|null URL of the generated certificate
     */
    public function generateCertificate()
    {
        if (!$this->attended || !$this->event->certificate_available) {
            return null;
        }
        
        // Placeholder for certificate generation logic
        // In a real implementation, this would:
        // 1. Generate a PDF certificate
        // 2. Store it in the system
        // 3. Update the model with certificate info
        
        $certificateUrl = route('certificates.view', [
            'event' => $this->event_id, 
            'user' => $this->user_id
        ]);
        
        $this->update([
            'certificate_issued' => true,
            'certificate_url' => $certificateUrl
        ]);
        
        return $certificateUrl;
    }

    /**
     * Send a reminder for the event.
     *
     * @return bool
     */
    public function sendReminder()
    {
        if ($this->reminder_sent || $this->status !== self::STATUS_CONFIRMED) {
            return false;
        }
        
        // Placeholder for notification logic
        // $this->user->notify(new EventReminderNotification($this->event));
        
        $this->update(['reminder_sent' => true]);
        
        return true;
    }

    /**
     * Get a value from the custom fields array.
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function getCustomField($key, $default = null)
    {
        $customFields = $this->custom_fields ?? [];
        
        return $customFields[$key] ?? $default;
    }

    /**
     * Set a value in the custom fields array.
     *
     * @param string $key
     * @param mixed $value
     * @return $this
     */
    public function setCustomField($key, $value)
    {
        $customFields = $this->custom_fields ?? [];
        $customFields[$key] = $value;
        
        $this->update(['custom_fields' => $customFields]);
        
        return $this;
    }

    /**
     * Check if the registration is confirmed.
     *
     * @return bool
     */
    public function isConfirmed()
    {
        return $this->status === self::STATUS_CONFIRMED;
    }

    /**
     * Check if the registration is waitlisted.
     *
     * @return bool
     */
    public function isWaitlisted()
    {
        return $this->status === self::STATUS_WAITLISTED;
    }

    /**
     * Check if the registration was canceled.
     *
     * @return bool
     */
    public function isCanceled()
    {
        return $this->status === self::STATUS_CANCELED;
    }

    /**
     * Check if the user has attended the event.
     *
     * @return bool
     */
    public function hasAttended()
    {
        return $this->attended;
    }

    /**
     * Check if a certificate has been issued.
     *
     * @return bool
     */
    public function hasCertificate()
    {
        return $this->certificate_issued && !empty($this->certificate_url);
    }

    /**
     * Scope a query to only include confirmed registrations.
     */
    public function scopeConfirmed($query)
    {
        return $query->where('status', self::STATUS_CONFIRMED);
    }

    /**
     * Scope a query to only include waitlisted registrations.
     */
    public function scopeWaitlisted($query)
    {
        return $query->where('status', self::STATUS_WAITLISTED);
    }

    /**
     * Scope a query to only include canceled registrations.
     */
    public function scopeCanceled($query)
    {
        return $query->where('status', self::STATUS_CANCELED);
    }

    /**
     * Scope a query to only include attended registrations.
     */
    public function scopeAttended($query)
    {
        return $query->where('attended', true);
    }

    /**
     * Scope a query to only include registrations for a specific event.
     */
    public function scopeForEvent($query, $eventId)
    {
        return $query->where('event_id', $eventId);
    }

    /**
     * Scope a query to only include registrations by a specific user.
     */
    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope a query to only include registrations with certificates.
     */
    public function scopeWithCertificates($query)
    {
        return $query->where('certificate_issued', true);
    }

    /**
     * Scope a query to only include registrations from a specific date range.
     */
    public function scopeRegisteredBetween($query, $startDate, $endDate)
    {
        return $query->whereBetween('registration_time', [$startDate, $endDate]);
    }
}
