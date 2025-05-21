<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Discount;

class Course extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'courses';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'course_id';

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
        'title',
        'description',
        'price',
        'instructor_id',
        'category_id',
        'approval_status',
        'thumbnail',
        'certificate_available',
        'certificate_type',
        'custom_certificate_path',
        'certificate_text'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'price' => 'decimal:2',
        'featured' => 'boolean',
        'certificate_available' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * The attributes that should be appended to arrays.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'is_published',
        'average_rating',
        'enrollment_count'
    ];

    /**
     * Get the status attribute (alias for approval_status for compatibility)
     *
     * @return string
     */
    public function getStatusAttribute()
    {
        // Map 'approved' to 'published' for backward compatibility
        if ($this->approval_status === 'approved') {
            return 'published';
        }
        return $this->approval_status;
    }

    /**
     * Get the is_published attribute (alias for approval_status for compatibility)
     *
     * @return bool
     */
    public function getIsPublishedAttribute()
    {
        // Return true if approval_status is 'approved'
        return $this->approval_status === 'approved';
    }

    /**
     * Set the status attribute (alias for approval_status for compatibility)
     *
     * @param string $value
     * @return void
     */
    public function setStatusAttribute($value)
    {
        // Map 'published' to 'approved' for backward compatibility
        if ($value === 'published') {
            $this->attributes['approval_status'] = 'approved';
        } else {
            $this->attributes['approval_status'] = $value;
        }
    }

    /**
     * Get the instructor that owns the course.
     */
    public function instructor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'instructor_id', 'user_id');
    }

    /**
     * Get the category that the course belongs to.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id', 'category_id');
    }

    /**
     * Get the videos for the course.
     */
    public function videos(): HasMany
    {
        return $this->hasMany(CourseVideo::class, 'course_id', 'course_id')
            ->orderBy('position', 'asc');
    }

    /**
     * Get the sections for the course.
     */
    public function sections(): HasMany
    {
        return $this->hasMany(CourseSection::class, 'course_id', 'course_id')
            ->orderBy('position', 'asc');
    }

    /**
     * Get the materials for the course.
     */
    public function materials(): HasMany
    {
        return $this->hasMany(CourseMaterial::class, 'course_id', 'course_id');
    }

    /**
     * Get the students enrolled in the course.
     */
    public function students(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'enrollments', 'course_id', 'student_id', 'course_id', 'user_id');
    }

    /**
     * Get the enrollments for the course.
     */
    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class, 'course_id', 'course_id');
    }

    /**
     * Get the ratings for the course.
     */
    public function ratings(): HasMany
    {
        return $this->hasMany(Rating::class, 'course_id', 'course_id');
    }

    /**
     * Get the reviews for the course.
     */
    public function reviews(): HasMany
    {
        // Check if course_reviews table exists
        if (\Illuminate\Support\Facades\Schema::hasTable('course_reviews')) {
            return $this->hasMany(CourseReview::class, 'course_id', 'course_id');
        }

        // Fallback to ratings table
        return $this->hasMany(Rating::class, 'course_id', 'course_id');
    }

    /**
     * Get the exams for the course.
     */
    public function exams(): HasMany
    {
        return $this->hasMany(Exam::class, 'course_id', 'course_id');
    }

    /**
     * Get the payments for the course.
     */
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class, 'course_id', 'course_id');
    }

    /**
     * Get student progress records for this course.
     */
    public function progress(): HasMany
    {
        return $this->hasMany(StudentProgress::class, 'course_id', 'course_id');
    }

    /**
     * Get the chats associated with this course.
     */
    public function chats(): HasMany
    {
        return $this->hasMany(Chat::class, 'course_id', 'course_id');
    }

    /**
     * Get the certificates issued for this course.
     */
    public function certificates(): HasMany
    {
        return $this->hasMany(Certificate::class, 'course_id', 'course_id');
    }

    /**
     * Calculate average rating for the course.
     */
    public function getAverageRatingAttribute()
    {
        // Check which rating column exists and use it
        if (\Illuminate\Support\Facades\Schema::hasColumn('ratings', 'rating_value')) {
            return $this->ratings()->avg('rating_value') ?? 0;
        }

        return $this->ratings()->avg('rating') ?? 0;
    }

    /**
     * Count total number of students enrolled.
     */
    public function getEnrollmentCountAttribute()
    {
        return $this->students()->count();
    }

    /**
     * Count total number of videos.
     */
    public function getVideoCountAttribute()
    {
        return $this->videos()->count();
    }

    /**
     * Count total number of materials.
     */
    public function getMaterialCountAttribute()
    {
        return $this->materials()->count();
    }

    /**
     * Boot the model.
     */
    public static function boot()
    {
        parent::boot();

        // Add default courses if there are no courses in the database
        if (self::count() === 0) {
            try {
                $instructorId = DB::table('users')
                    ->join('user_roles', 'users.user_id', '=', 'user_roles.user_id')
                    ->where('user_roles.role', 'instructor')
                    ->value('users.user_id');

                if (!$instructorId) {
                    $instructorId = DB::table('users')->insertGetId([
                        'name' => 'John Doe',
                        'email' => 'instructor@example.com',
                        'password' => bcrypt('password'),
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);

                    DB::table('user_roles')->insert([
                        'user_id' => $instructorId,
                        'role' => 'instructor',
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                }

                $categoryId = DB::table('categories')->value('category_id');
                if (!$categoryId) {
                    $categoryId = DB::table('categories')->insertGetId([
                        'name' => 'Programming',
                        'description' => 'Programming and development courses',
                        'slug' => 'programming',
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                }

                // Create default courses
                $defaultCourses = [
                    [
                        'title' => 'PHP Basics',
                        'description' => 'Learn PHP basics and how to use it in web development.',
                        'price' => 299.99,
                        'duration' => 24,
                        'level' => 'beginner',
                        'language' => 'English',
                        'featured' => true,
                        'approval_status' => 'approved',
                    ],
                    [
                        'title' => 'Laravel Web Development',
                        'description' => 'Comprehensive course in web development using Laravel framework.',
                        'price' => 499.99,
                        'duration' => 36,
                        'level' => 'intermediate',
                        'language' => 'English',
                        'featured' => true,
                        'approval_status' => 'approved',
                    ],
                    [
                        'title' => 'React UI Development',
                        'description' => 'Learn how to develop interactive and dynamic user interfaces using React.js.',
                        'price' => 399.99,
                        'duration' => 30,
                        'level' => 'intermediate',
                        'language' => 'English',
                        'featured' => true,
                        'approval_status' => 'approved',
                    ],
                ];

                foreach ($defaultCourses as $course) {
                    DB::table('courses')->insert([
                        'title' => $course['title'],
                        'description' => $course['description'],
                        'instructor_id' => $instructorId,
                        'category_id' => $categoryId,
                        'price' => $course['price'],
                        'duration' => $course['duration'],
                        'level' => $course['level'],
                        'language' => $course['language'],
                        'featured' => $course['featured'],
                        'approval_status' => $course['approval_status'],
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            } catch (\Exception $e) {
                Log::error('Error creating default courses: ' . $e->getMessage());
            }
        }
    }

    /**
     * Get active discounts for this course.
     */
    public function getActiveDiscounts()
    {
        $now = now();
        
        return Discount::where(function($query) {
                $query->where('applies_to_all_courses', true)
                      ->orWhereJsonContains('courses', $this->course_id);
            })
            ->where('is_active', true)
            ->where(function($query) use ($now) {
                $query->whereNull('start_date')
                      ->orWhere('start_date', '<=', $now);
            })
            ->where(function($query) use ($now) {
                $query->whereNull('end_date')
                      ->orWhere('end_date', '>=', $now);
            })
            ->get();
    }
    
    /**
     * Get the final discounted price for this course.
     */
    public function getDiscountedPrice()
    {
        $discounts = $this->getActiveDiscounts();
        
        if ($discounts->isEmpty()) {
            return $this->price;
        }
        
        $lowestPrice = $this->price;
        
        foreach ($discounts as $discount) {
            $discountedPrice = $discount->calculateDiscountedPrice($this->price);
            $lowestPrice = min($lowestPrice, $discountedPrice);
        }
        
        return $lowestPrice;
    }
    
    /**
     * Check if a coupon is applicable to this course.
     */
    public function isCouponApplicable($coupon)
    {
        if (!$coupon->isValid()) {
            return false;
        }
        
        $coursesApplicable = $coupon->courses_applicable ?? [];
        
        return empty($coursesApplicable) || in_array($this->course_id, $coursesApplicable);
    }
    
    /**
     * Get the final price after applying a coupon.
     */
    public function getPriceWithCoupon($coupon)
    {
        if (!$this->isCouponApplicable($coupon)) {
            return $this->getDiscountedPrice();
        }
        
        $discountedPrice = $this->getDiscountedPrice();
        
        // If the coupon is fixed type
        if ($coupon->type === 'fixed') {
            return max(0, $discountedPrice - $coupon->value);
        }
        
        // If the coupon is percentage type
        return $discountedPrice * (1 - ($coupon->value / 100));
    }
}
