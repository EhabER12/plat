<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdminNotification extends Model
{
    use HasFactory;

    /**
     * الخصائص التي يمكن ملؤها بشكل جماعي.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'type',
        'title',
        'message',
        'is_read',
        'severity'
    ];

    /**
     * تحويل أنواع البيانات عند استرجاعها من قاعدة البيانات.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_read' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * تعليم الإشعار كمقروء.
     */
    public function markAsRead()
    {
        $this->update([
            'is_read' => true
        ]);
        
        return $this;
    }

    /**
     * نطاق للاستعلام عن الإشعارات غير المقروءة.
     */
    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    /**
     * نطاق للاستعلام حسب النوع.
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * نطاق للاستعلام حسب مستوى الأهمية.
     */
    public function scopeHighSeverity($query)
    {
        return $query->where('severity', 'high');
    }
}
