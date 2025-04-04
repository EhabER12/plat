<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class UserRole extends Pivot
{
    protected $table = 'user_roles';

    protected $fillable = [
        'user_id',
        'role'
    ];

    public $timestamps = true;
    
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
} 