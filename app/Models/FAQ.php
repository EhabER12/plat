<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FAQ extends Model
{
    use HasFactory;

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'faq_id';
    
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'faqs';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'question',
        'answer',
        'category_id',
        'course_id',
        'created_by',
        'updated_by',
        'display_order',
        'is_published',
        'published_at',
        'views_count',
        'slug'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'display_order' => 'integer',
        'is_published' => 'boolean',
        'published_at' => 'datetime',
        'views_count' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Get the category this FAQ belongs to.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    /**
     * Get the course this FAQ belongs to (if applicable).
     */
    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class, 'course_id', 'course_id');
    }

    /**
     * Get the user who created this FAQ.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by', 'user_id');
    }

    /**
     * Get the user who last updated this FAQ.
     */
    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by', 'user_id');
    }

    /**
     * Increment the view count for this FAQ.
     *
     * @return $this
     */
    public function incrementViews()
    {
        $this->increment('views_count');
        return $this;
    }

    /**
     * Publish the FAQ.
     *
     * @return $this
     */
    public function publish()
    {
        $this->update([
            'is_published' => true,
            'published_at' => now()
        ]);
        
        return $this;
    }

    /**
     * Unpublish the FAQ.
     *
     * @return $this
     */
    public function unpublish()
    {
        $this->update([
            'is_published' => false,
            'published_at' => null
        ]);
        
        return $this;
    }

    /**
     * Get a short excerpt of the answer.
     *
     * @param int $length
     * @return string
     */
    public function getExcerpt($length = 100)
    {
        return \Str::limit(strip_tags($this->answer), $length);
    }

    /**
     * Generate a slug from the question if not already set.
     *
     * @return void
     */
    protected static function booted()
    {
        static::creating(function ($faq) {
            if (empty($faq->slug)) {
                $faq->slug = \Str::slug($faq->question);
            }
        });
    }

    /**
     * Scope a query to only include published FAQs.
     */
    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    /**
     * Scope a query to only include unpublished FAQs.
     */
    public function scopeUnpublished($query)
    {
        return $query->where('is_published', false);
    }

    /**
     * Scope a query to order by display order.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('display_order', 'asc');
    }

    /**
     * Scope a query to only include FAQs for a specific course.
     */
    public function scopeForCourse($query, $courseId)
    {
        return $query->where('course_id', $courseId);
    }

    /**
     * Scope a query to only include FAQs in a specific category.
     */
    public function scopeInCategory($query, $categoryId)
    {
        return $query->where('category_id', $categoryId);
    }

    /**
     * Scope a query to search for FAQs by question or answer.
     */
    public function scopeSearch($query, $searchTerm)
    {
        return $query->where('question', 'LIKE', "%{$searchTerm}%")
                    ->orWhere('answer', 'LIKE', "%{$searchTerm}%");
    }

    /**
     * Scope a query to order by popularity (views count).
     */
    public function scopePopular($query)
    {
        return $query->orderBy('views_count', 'desc');
    }

    /**
     * Scope a query to order by recently published.
     */
    public function scopeRecent($query)
    {
        return $query->orderBy('published_at', 'desc');
    }
}
