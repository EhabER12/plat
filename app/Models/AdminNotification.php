<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

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
        'user_id',
        'related_id',
        'related_type',
        'content',
        'data',
        'is_read',
        'read_at',
        'severity'
    ];

    /**
     * تحويل أنواع البيانات عند استرجاعها من قاعدة البيانات.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_read' => 'boolean',
        'read_at' => 'datetime',
        'data' => 'array',
        'severity' => 'integer'
    ];

    /**
     * العلاقة مع المستخدم المرتبط بالإشعار.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    /**
     * العلاقة مع العنصر المرتبط (قد يكون رسالة، مستخدم، إلخ).
     */
    public function related(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * تعليم الإشعار كمقروء.
     */
    public function markAsRead()
    {
        $this->update([
            'is_read' => true,
            'read_at' => now()
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
    public function scopeMinSeverity($query, $level)
    {
        return $query->where('severity', '>=', $level);
    }
}
