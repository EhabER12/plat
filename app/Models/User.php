<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'users';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'user_id';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = true;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password_hash',
        'phone',
        'address',
        'bio',
        'profile_picture',
        'dob',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password_hash',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'dob' => 'date',
    ];

    /**
     * Get the password for the user.
     *
     * @return string
     */
    public function getAuthPassword()
    {
        return $this->password_hash;
    }

    /**
     * Set the user's password.
     *
     * @param  string  $value
     * @return void
     */
    public function setPasswordAttribute($value)
    {
        $this->attributes['password_hash'] = $value;
    }

    /**
     * Get user roles relationship.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function roles()
    {
        return $this->hasMany('App\Models\UserRole', 'user_id', 'user_id');
    }

    /**
     * Get all roles for the user.
     *
     * @return array
     */
    public function getUserRoles()
    {
        return DB::table('user_roles')
            ->where('user_id', $this->user_id)
            ->pluck('role')
            ->toArray();
    }

    /**
     * Check if user has a specific role.
     *
     * @param  string  $role
     * @return bool
     */
    public function hasRole($role)
    {
        return in_array($role, $this->getUserRoles());
    }

    /**
     * Get the courses that the user has created as instructor.
     */
    public function courses()
    {
        return $this->hasMany(Course::class, 'instructor_id', 'user_id');
    }

    /**
     * Get the enrollments for the user.
     */
    public function enrollments()
    {
        return $this->hasMany(Enrollment::class, 'student_id', 'user_id');
    }

    /**
     * Get the courses that the user is enrolled in.
     */
    public function enrolledCourses()
    {
        return $this->belongsToMany(Course::class, 'enrollments', 'student_id', 'course_id')
                    ->withPivot('enrolled_at', 'completion_date', 'progress')
                    ->withTimestamps();
    }

    /**
     * Get the reviews written by the user.
     */
    public function reviews()
    {
        // Check if course_reviews table exists
        if (\Illuminate\Support\Facades\Schema::hasTable('course_reviews')) {
            return $this->hasMany(CourseReview::class, 'user_id', 'user_id');
        }

        // Fallback to Review model
        return $this->hasMany(Review::class, 'user_id', 'user_id');
    }

    /**
     * Get the verification request for instructor.
     */
    public function instructorVerification()
    {
        return $this->hasOne(InstructorVerification::class, 'user_id', 'user_id');
    }

    /**
     * Get user's support tickets.
     */
    public function supportTickets(): HasMany
    {
        return $this->hasMany(SupportTicket::class, 'user_id');
    }

    /**
     * Get user's payments.
     */
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class, 'user_id');
    }

    /**
     * Get user's withdrawals (for instructors).
     */
    public function withdrawals(): HasMany
    {
        return $this->hasMany(Withdrawal::class, 'instructor_id');
    }

    /**
     * Get instructor's payment accounts.
     */
    public function paymentAccounts(): HasMany
    {
        return $this->hasMany(InstructorPaymentAccount::class, 'instructor_id');
    }

    /**
     * Get instructor's earnings.
     */
    public function earnings(): HasMany
    {
        return $this->hasMany(InstructorEarning::class, 'instructor_id');
    }

    /**
     * Get instructor's default payment account.
     */
    public function defaultPaymentAccount()
    {
        return $this->paymentAccounts()->where('is_default', true)->first();
    }

    /**
     * Get instructor's total available earnings.
     */
    public function getAvailableEarningsAttribute()
    {
        return $this->earnings()->where('status', 'available')->sum('amount');
    }

    /**
     * Get instructor's total pending earnings.
     */
    public function getPendingEarningsAttribute()
    {
        return $this->earnings()->where('status', 'pending')->sum('amount');
    }

    /**
     * Get instructor's total withdrawn earnings.
     */
    public function getWithdrawnEarningsAttribute()
    {
        return $this->earnings()->where('status', 'withdrawn')->sum('amount');
    }

    /**
     * Get user's ratings given to courses.
     */
    public function ratings(): HasMany
    {
        // Check if ratings table has student_id column
        if (\Illuminate\Support\Facades\Schema::hasColumn('ratings', 'student_id')) {
            return $this->hasMany(Rating::class, 'student_id');
        }

        // Fallback to user_id if student_id doesn't exist
        return $this->hasMany(Rating::class, 'user_id');
    }

    /**
     * Get student progress for enrolled courses.
     */
    public function progress(): HasMany
    {
        return $this->hasMany(StudentProgress::class, 'student_id');
    }

    /**
     * Get user's chat participations.
     */
    public function chatParticipations(): BelongsToMany
    {
        return $this->belongsToMany(Chat::class, 'chat_participants', 'user_id', 'chat_id')
            ->withPivot(['is_admin', 'last_read_at']);
    }

    /**
     * Get user's sent messages.
     */
    public function messages(): HasMany
    {
        return $this->hasMany(Message::class, 'sender_id');
    }

    /**
     * Get user's notifications.
     */
    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class, 'user_id');
    }

    /**
     * Get the students linked to this parent user.
     */
    public function students(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'parent_student', 'parent_id', 'student_id')
            ->withPivot(['is_approved', 'relation'])
            ->withTimestamps();
    }

    /**
     * Get the parents linked to this student user.
     */
    public function parents(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'parent_student', 'student_id', 'parent_id')
            ->withPivot(['is_approved', 'relation'])
            ->withTimestamps();
    }

    /**
     * Get user's exam attempts.
     */
    public function examAttempts(): HasMany
    {
        return $this->hasMany(ExamAttempt::class, 'student_id');
    }
}
