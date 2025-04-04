<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Language extends Model
{
    use HasFactory;

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'language_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'code',
        'native_name',
        'flag',
        'is_default',
        'is_rtl',
        'is_active',
        'display_order',
        'locale'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_default' => 'boolean',
        'is_rtl' => 'boolean',
        'is_active' => 'boolean',
        'display_order' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Set this language as the default one.
     * This will remove the default flag from any other language.
     *
     * @return $this
     */
    public function setAsDefault()
    {
        if (!$this->is_default) {
            // Remove default flag from the current default language
            self::where('is_default', true)->update(['is_default' => false]);
            
            // Set this language as default
            $this->update(['is_default' => true]);
        }
        
        return $this;
    }

    /**
     * Activate this language.
     *
     * @return $this
     */
    public function activate()
    {
        if (!$this->is_active) {
            $this->update(['is_active' => true]);
        }
        
        return $this;
    }

    /**
     * Deactivate this language.
     * The default language cannot be deactivated.
     *
     * @return $this
     */
    public function deactivate()
    {
        if ($this->is_active && !$this->is_default) {
            $this->update(['is_active' => false]);
        }
        
        return $this;
    }

    /**
     * Get the flag URL.
     *
     * @return string|null
     */
    public function getFlagUrlAttribute()
    {
        if (!$this->flag) {
            return null;
        }
        
        // Check if the flag is a URL
        if (filter_var($this->flag, FILTER_VALIDATE_URL)) {
            return $this->flag;
        }
        
        // Check if it's a media library id
        $media = MediaLibrary::find($this->flag);
        if ($media) {
            return $media->url;
        }
        
        // Assume it's a path relative to the public directory
        return asset($this->flag);
    }

    /**
     * Get the language direction.
     *
     * @return string
     */
    public function getDirectionAttribute()
    {
        return $this->is_rtl ? 'rtl' : 'ltr';
    }

    /**
     * Get the default language.
     *
     * @return Language|null
     */
    public static function getDefault()
    {
        return self::where('is_default', true)->first();
    }

    /**
     * Get a list of active language codes.
     *
     * @return array
     */
    public static function getActiveCodes()
    {
        return self::where('is_active', true)
                  ->pluck('code')
                  ->toArray();
    }

    /**
     * Get available languages with name and code.
     *
     * @param bool $activeOnly
     * @return array
     */
    public static function getAvailableLanguages($activeOnly = true)
    {
        $query = self::select('name', 'code', 'native_name', 'is_default', 'is_rtl');
        
        if ($activeOnly) {
            $query->where('is_active', true);
        }
        
        return $query->orderBy('display_order', 'asc')
                    ->get()
                    ->toArray();
    }

    /**
     * Check if a language code exists and is active.
     *
     * @param string $code
     * @return bool
     */
    public static function isValidCode($code)
    {
        return self::where('code', $code)
                  ->where('is_active', true)
                  ->exists();
    }

    /**
     * Scope a query to only include active languages.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to only include RTL languages.
     */
    public function scopeRtl($query)
    {
        return $query->where('is_rtl', true);
    }

    /**
     * Scope a query to order by display order.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('display_order', 'asc');
    }
}
