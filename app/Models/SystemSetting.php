<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class SystemSetting extends Model
{
    use HasFactory;
    
    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'setting_id';
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'key',
        'value',
        'value_type',
        'group',
        'display_name',
        'description',
        'is_public',
        'is_core',
        'created_by',
        'updated_by',
    ];
    
    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'value' => 'json',
        'is_public' => 'boolean',
        'is_core' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
    
    /**
     * Common setting types.
     */
    const TYPE_STRING = 'string';
    const TYPE_INTEGER = 'integer';
    const TYPE_FLOAT = 'float';
    const TYPE_BOOLEAN = 'boolean';
    const TYPE_ARRAY = 'array';
    const TYPE_OBJECT = 'object';
    const TYPE_FILE = 'file';
    const TYPE_IMAGE = 'image';
    
    /**
     * Common setting groups.
     */
    const GROUP_GENERAL = 'general';
    const GROUP_EMAIL = 'email';
    const GROUP_PAYMENT = 'payment';
    const GROUP_COURSES = 'courses';
    const GROUP_USERS = 'users';
    const GROUP_APPEARANCE = 'appearance';
    const GROUP_INTEGRATIONS = 'integrations';
    const GROUP_SECURITY = 'security';
    
    /**
     * Get the creator of this setting.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
    
    /**
     * Get the last updater of this setting.
     */
    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
    
    /**
     * Get a setting value by key.
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public static function get($key, $default = null)
    {
        // Try to get from cache first
        $cachedValue = Cache::get('system_settings:' . $key);
        if ($cachedValue !== null) {
            return $cachedValue;
        }
        
        // Try to get from database
        $setting = self::where('key', $key)->first();
        
        if (!$setting) {
            return $default;
        }
        
        $value = $setting->value;
        
        // Cast the value based on its type
        switch ($setting->value_type) {
            case self::TYPE_INTEGER:
                $value = (int) $value;
                break;
            case self::TYPE_FLOAT:
                $value = (float) $value;
                break;
            case self::TYPE_BOOLEAN:
                $value = (bool) $value;
                break;
            case self::TYPE_ARRAY:
            case self::TYPE_OBJECT:
                // Already cast by the model's $casts property
                break;
        }
        
        // Cache the value for future use
        Cache::put('system_settings:' . $key, $value, now()->addHour());
        
        return $value;
    }
    
    /**
     * Set a setting value by key.
     *
     * @param string $key
     * @param mixed $value
     * @param string|null $group
     * @param string|null $valueType
     * @param string|null $displayName
     * @param string|null $description
     * @param bool $isPublic
     * @param bool $isCore
     * @param int|null $updatedBy
     * @return SystemSetting
     */
    public static function set(
        $key, 
        $value, 
        $group = null, 
        $valueType = null, 
        $displayName = null, 
        $description = null, 
        $isPublic = false, 
        $isCore = false, 
        $updatedBy = null
    ) {
        // Determine value type if not provided
        if (!$valueType) {
            if (is_string($value)) {
                $valueType = self::TYPE_STRING;
            } elseif (is_int($value)) {
                $valueType = self::TYPE_INTEGER;
            } elseif (is_float($value)) {
                $valueType = self::TYPE_FLOAT;
            } elseif (is_bool($value)) {
                $valueType = self::TYPE_BOOLEAN;
            } elseif (is_array($value)) {
                $valueType = self::TYPE_ARRAY;
            } elseif (is_object($value)) {
                $valueType = self::TYPE_OBJECT;
            } else {
                $valueType = self::TYPE_STRING;
            }
        }
        
        // Try to find existing setting
        $setting = self::where('key', $key)->first();
        
        if (!$setting) {
            // Create new setting
            $setting = new self([
                'key' => $key,
                'value' => $value,
                'value_type' => $valueType,
                'group' => $group ?? self::GROUP_GENERAL,
                'display_name' => $displayName ?? $key,
                'description' => $description,
                'is_public' => $isPublic,
                'is_core' => $isCore,
                'created_by' => $updatedBy,
                'updated_by' => $updatedBy,
            ]);
        } else {
            // Update existing setting
            $setting->value = $value;
            
            if ($valueType) {
                $setting->value_type = $valueType;
            }
            
            if ($group) {
                $setting->group = $group;
            }
            
            if ($displayName) {
                $setting->display_name = $displayName;
            }
            
            if ($description) {
                $setting->description = $description;
            }
            
            if ($updatedBy) {
                $setting->updated_by = $updatedBy;
            }
        }
        
        $setting->save();
        
        // Update cache
        Cache::put('system_settings:' . $key, $value, now()->addHour());
        
        return $setting;
    }
    
    /**
     * Delete a setting by key.
     *
     * @param string $key
     * @return bool
     */
    public static function remove($key)
    {
        $result = self::where('key', $key)->delete();
        Cache::forget('system_settings:' . $key);
        return $result > 0;
    }
    
    /**
     * Clear all settings cache.
     *
     * @return void
     */
    public static function clearCache()
    {
        $settings = self::all();
        
        foreach ($settings as $setting) {
            Cache::forget('system_settings:' . $setting->key);
        }
    }
    
    /**
     * Scope a query to only include public settings.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }
    
    /**
     * Scope a query to filter settings by group.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $group
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeInGroup($query, $group)
    {
        return $query->where('group', $group);
    }
}
