<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Admin extends Model
{
    use Notifiable;

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
     * تحديد البريد الإلكتروني الذي سيتم استخدامه للإشعارات
     *
     * @return string
     */
    public function routeNotificationForMail()
    {
        // يمكنك تغيير هذا البريد الإلكتروني إلى البريد الإلكتروني للمشرف الخاص بك
        return 'admin@youreducationapp.com';
    }

    /**
     * Scope a query to only include admin users.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeAdmins($query)
    {
        return $query->whereHas('roles', function($q) {
            $q->where('role', 'admin');
        });
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
} 