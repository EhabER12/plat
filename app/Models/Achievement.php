<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Achievement extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'description',
        'icon',
        'points',
        'criteria',
        'is_active'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'points' => 'integer',
        'criteria' => 'array',
        'is_active' => 'boolean',
    ];

    /**
     * Get the students who have earned this achievement.
     */
    public function students(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'student_achievements', 'achievement_id', 'user_id')
            ->withPivot('earned_at')
            ->withTimestamps();
    }
}
