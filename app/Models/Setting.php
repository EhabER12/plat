<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    use HasFactory;

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'key',
        'value',
        'description',
        'updated_by'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'updated_at' => 'datetime',
    ];

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * Get a setting by key.
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public static function get($key, $default = null)
    {
        $setting = self::where('key', $key)->first();
        return $setting ? $setting->value : $default;
    }

    /**
     * Set a setting value.
     *
     * @param string $key
     * @param mixed $value
     * @param int|null $userId
     * @return Setting
     */
    public static function set($key, $value, $userId = null)
    {
        $setting = self::where('key', $key)->first();
        
        if ($setting) {
            $setting->value = $value;
            $setting->updated_at = now();
            $setting->updated_by = $userId;
            $setting->save();
        } else {
            $setting = self::create([
                'key' => $key,
                'value' => $value,
                'updated_at' => now(),
                'updated_by' => $userId
            ]);
        }
        
        return $setting;
    }

    /**
     * Get all settings as a key-value array.
     *
     * @return array
     */
    public static function getAllSettings()
    {
        return self::pluck('value', 'key')->toArray();
    }

    /**
     * Clear settings cache.
     *
     * @return void
     */
    public static function clearCacheSettings()
    {
        $settings = self::all();
        foreach ($settings as $setting) {
            Cache::forget('setting_' . $setting->key);
        }
        Cache::forget('settings_all');
    }
}
