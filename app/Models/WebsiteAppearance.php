<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class WebsiteAppearance extends Model
{
    use HasFactory;

    protected $primaryKey = 'id';
    
    protected $fillable = [
        'key',
        'value',
        'section',
        'type',
    ];

    protected $casts = [
        'value' => 'json',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Section types
     */
    const SECTION_HERO = 'hero';
    const SECTION_FEATURES = 'features';
    const SECTION_ABOUT = 'about';
    const SECTION_FOOTER = 'footer';
    const SECTION_NAVBAR_BANNER = 'navbar_banner';
    
    /**
     * Value types
     */
    const TYPE_TEXT = 'text';
    const TYPE_IMAGE = 'image';
    const TYPE_COLOR = 'color';
    const TYPE_NUMBER = 'number';
    const TYPE_BOOLEAN = 'boolean';
    const TYPE_JSON = 'json';

    /**
     * Get a setting by key and section
     *
     * @param string $key
     * @param string|null $section
     * @param mixed $default
     * @return mixed
     */
    public static function getSetting($key, $section = null, $default = null)
    {
        $cacheKey = "website_appearance:{$section}:{$key}";
        
        if (Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }
        
        $query = self::where('key', $key);
        
        if ($section) {
            $query->where('section', $section);
        }
        
        $setting = $query->first();
        
        if (!$setting) {
            return $default;
        }
        
        Cache::put($cacheKey, $setting->value, now()->addDay());
        
        return $setting->value;
    }

    /**
     * Set a setting
     *
     * @param string $key
     * @param mixed $value
     * @param string $section
     * @param string $type
     * @return WebsiteAppearance
     */
    public static function setSetting($key, $value, $section, $type = self::TYPE_TEXT)
    {
        $setting = self::firstOrNew([
            'key' => $key,
            'section' => $section,
        ]);
        
        $setting->value = $value;
        $setting->type = $type;
        $setting->save();
        
        $cacheKey = "website_appearance:{$section}:{$key}";
        Cache::put($cacheKey, $value, now()->addDay());
        
        return $setting;
    }

    /**
     * Get all settings for a section
     *
     * @param string $section
     * @return array
     */
    public static function getSection($section)
    {
        $cacheKey = "website_appearance:section:{$section}";
        
        if (Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }
        
        $settings = self::where('section', $section)->get();
        $result = [];
        
        foreach ($settings as $setting) {
            $result[$setting->key] = $setting->value;
        }
        
        Cache::put($cacheKey, $result, now()->addDay());
        
        return $result;
    }

    /**
     * Clear the cache for all website appearance settings
     *
     * @return void
     */
    public static function clearCache()
    {
        Cache::forget('website_appearance:*');
    }
} 