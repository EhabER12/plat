<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

class Tag extends Model
{
    use HasFactory;

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'tag_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'slug',
        'type',
        'description',
        'color'
    ];

    /**
     * The "booted" method of the model.
     */
    protected static function booted()
    {
        static::creating(function ($tag) {
            if (empty($tag->slug)) {
                $tag->slug = Str::slug($tag->name);
            }
        });
    }

    /**
     * Get the courses that are tagged with this tag.
     */
    public function courses(): BelongsToMany
    {
        return $this->belongsToMany(
            Course::class,
            'course_tags',
            'tag_id',
            'course_id'
        );
    }

    /**
     * Get the blog posts that are tagged with this tag.
     */
    public function blogPosts(): BelongsToMany
    {
        return $this->belongsToMany(
            BlogPost::class,
            'blog_post_tags',
            'tag_id',
            'post_id'
        );
    }

    /**
     * Get the count of items tagged with this tag.
     */
    public function getItemsCountAttribute()
    {
        return $this->courses()->count() + $this->blogPosts()->count();
    }

    /**
     * Scope a query to only include tags of a specific type.
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope a query to search for tags by name.
     */
    public function scopeSearch($query, $search)
    {
        return $query->where('name', 'like', "%{$search}%")
                    ->orWhere('slug', 'like', "%{$search}%");
    }

    /**
     * Get popular tags based on usage.
     */
    public static function getPopularTags($limit = 10, $type = null)
    {
        $query = self::withCount(['courses', 'blogPosts'])
                    ->orderByRaw('courses_count + blog_posts_count DESC');
        
        if ($type) {
            $query->where('type', $type);
        }
        
        return $query->limit($limit)->get();
    }

    /**
     * Get or create a tag by name.
     */
    public static function findOrCreateByName($name, $type = 'general')
    {
        $slug = Str::slug($name);
        
        return self::firstOrCreate(
            ['slug' => $slug],
            [
                'name' => $name,
                'type' => $type
            ]
        );
    }
}
