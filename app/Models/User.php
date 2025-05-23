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
        'password',
        'phone',
        'address',
        'bio',
        'detailed_description',
        'website',
        'linkedin_profile',
        'twitter_profile',
        'profile_picture',
        'banner_image',
        'dob',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
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
        return $this->password;
    }

    /**
     * Set the user's password.
     *
     * @param  string  $value
     * @return void
     */
    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = $value;
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
        return $this->hasOne(\App\Models\InstructorVerification::class, 'user_id');
    }

    /**
     * Get user's support tickets.
     */
    public function supportTickets(): HasMany
    {
        return $this->hasMany(SupportTicket::class, 'user_id', 'user_id');
    }

    /**
     * Get user's payments.
     */
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class, 'user_id', 'user_id');
    }

    /**
     * Get user's withdrawals (for instructors).
     */
    public function withdrawals(): HasMany
    {
        return $this->hasMany(Withdrawal::class, 'instructor_id', 'user_id');
    }

    /**
     * Get instructor's payment accounts.
     */
    public function paymentAccounts(): HasMany
    {
        return $this->hasMany(InstructorPaymentAccount::class, 'instructor_id', 'user_id');
    }

    /**
     * Get instructor's earnings.
     */
    public function earnings(): HasMany
    {
        return $this->hasMany(InstructorEarning::class, 'instructor_id', 'user_id');
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
     * Get instructor's total earnings.
     */
    public function getTotalEarningsAttribute()
    {
        return $this->earnings()->sum('amount');
    }

    /**
     * Get user's ratings given to courses.
     */
    public function ratings(): HasMany
    {
        return $this->hasMany(Rating::class, 'user_id', 'user_id');
    }

    /**
     * Get student progress for enrolled courses.
     */
    public function progress(): HasMany
    {
        return $this->hasMany(StudentProgress::class, 'student_id', 'user_id');
    }

    /**
     * Get user's chat participations.
     */
    public function chatParticipations(): BelongsToMany
    {
        return $this->belongsToMany(Chat::class, 'chat_participants', 'user_id', 'chat_id');
    }

    /**
     * Get user's messages.
     */
    public function messages(): HasMany
    {
        return $this->hasMany(Message::class, 'user_id', 'user_id');
    }

    /**
     * Get user's notifications.
     */
    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class, 'user_id', 'user_id');
    }

    /**
     * Get the students linked to this parent user.
     */
    public function students(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'parent_student_relations', 'parent_id', 'student_id')
                    ->withPivot('relationship', 'status', 'created_at')
                    ->withTimestamps();
    }

    /**
     * Get the parents linked to this student user.
     */
    public function parents(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'parent_student_relations', 'student_id', 'parent_id')
                    ->withPivot('relationship', 'status', 'created_at')
                    ->withTimestamps();
    }

    /**
     * Get all verified parents of this student.
     */
    public function verifiedParents()
    {
        return $this->belongsToMany(User::class, 'parent_student_relations', 'student_id', 'parent_id')
                    ->wherePivot('status', 'approved')
                    ->withTimestamps();
    }

    /**
     * Get all verified students of this parent.
     */
    public function verifiedStudents()
    {
        return $this->belongsToMany(User::class, 'parent_student_relations', 'parent_id', 'student_id')
                    ->wherePivot('status', 'approved')
                    ->withTimestamps();
    }

    /**
     * Get user's exam attempts.
     */
    public function examAttempts(): HasMany
    {
        return $this->hasMany(ExamAttempt::class, 'user_id', 'user_id');
    }

    /**
     * Get the parent relations where this user is a parent.
     */
    public function parentRelations()
    {
        return $this->hasMany(ParentStudentRelation::class, 'parent_id', 'user_id');
    }

    /**
     * Get the student relations where this user is a student.
     */
    public function studentRelations()
    {
        return $this->hasMany(ParentStudentRelation::class, 'student_id', 'user_id');
    }

    /**
     * Get the books that the user has created as instructor.
     */
    public function books()
    {
        return $this->hasMany(Book::class, 'user_id', 'user_id');
    }

    /**
     * Get the books that the user has purchased.
     */
    public function purchasedBooks()
    {
        return $this->belongsToMany(Book::class, 'book_purchases', 'user_id', 'book_id')
                    ->withPivot('purchase_id', 'amount', 'status', 'purchased_at')
                    ->withTimestamps();
    }

    /**
     * Get the book purchases made by the user.
     */
    public function bookPurchases()
    {
        return $this->hasMany(BookPurchase::class, 'user_id', 'user_id');
    }

    /**
     * Get the badges earned by the user.
     */
    public function badges(): BelongsToMany
    {
        return $this->belongsToMany(Badge::class, 'student_badges', 'user_id', 'badge_id')
                    ->withPivot('earned_at')
                    ->withTimestamps();
    }

    /**
     * Get the achievements earned by the user.
     */
    public function achievements(): BelongsToMany
    {
        return $this->belongsToMany(Achievement::class, 'student_achievements', 'user_id', 'achievement_id')
                    ->withPivot('earned_at')
                    ->withTimestamps();
    }

    /**
     * Get the quiz attempts made by the user.
     */
    public function quizAttempts(): HasMany
    {
        return $this->hasMany(QuizAttempt::class, 'user_id', 'user_id');
    }

    /**
     * The "booted" method of the model.
     */
    protected static function booted()
    {
        static::created(function ($user) {
            // إذا لم يكن هناك قيمة لـ user_id، قم بتعيينها من id
            if (empty($user->user_id) && !empty($user->id)) {
                $user->user_id = $user->id;
                $user->save();
            }
        });
    }
}
