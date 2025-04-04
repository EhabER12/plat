<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Wishlist extends Model
{
    use HasFactory;

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'wishlist_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'course_id',
        'added_at'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'added_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Get the user who added the course to wishlist.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    /**
     * Get the course that was added to wishlist.
     */
    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class, 'course_id', 'course_id');
    }

    /**
     * Scope a query to only include wishlisted items for a specific user.
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Check if the course is currently on sale.
     */
    public function isOnSale()
    {
        return $this->course && $this->course->isOnSale();
    }

    /**
     * Check if the course has been recently added to the user's wishlist.
     */
    public function isRecentlyAdded($days = 7)
    {
        return $this->added_at && $this->added_at->diffInDays(now()) <= $days;
    }

    /**
     * Add a course to a user's wishlist.
     */
    public static function addToWishlist($userId, $courseId)
    {
        return self::firstOrCreate([
            'user_id' => $userId,
            'course_id' => $courseId,
        ], [
            'added_at' => now(),
        ]);
    }

    /**
     * Remove a course from a user's wishlist.
     */
    public static function removeFromWishlist($userId, $courseId)
    {
        return self::where('user_id', $userId)
                  ->where('course_id', $courseId)
                  ->delete();
    }
}
