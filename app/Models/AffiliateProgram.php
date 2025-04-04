<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AffiliateProgram extends Model
{
    use HasFactory;

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'program_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'description',
        'commission_rate',
        'commission_type',
        'cookie_days',
        'min_payout',
        'payout_method',
        'terms_conditions',
        'is_active',
        'apply_to_all_courses',
        'eligible_courses',
        'created_by',
        'approved_domains',
        'banned_domains',
        'custom_rules'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'commission_rate' => 'decimal:2',
        'cookie_days' => 'integer',
        'min_payout' => 'decimal:2',
        'is_active' => 'boolean',
        'apply_to_all_courses' => 'boolean',
        'eligible_courses' => 'array',
        'approved_domains' => 'array',
        'banned_domains' => 'array',
        'custom_rules' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Commission type constants
     */
    const TYPE_PERCENTAGE = 'percentage';
    const TYPE_FIXED = 'fixed';

    /**
     * Payout method constants
     */
    const METHOD_PAYPAL = 'paypal';
    const METHOD_BANK_TRANSFER = 'bank_transfer';
    const METHOD_CREDIT = 'account_credit';
    const METHOD_CHEQUE = 'cheque';
    const METHOD_WALLET = 'wallet';
    
    /**
     * Get the user who created this program.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by', 'user_id');
    }

    /**
     * Get the affiliates enrolled in this program.
     */
    public function affiliates(): HasMany
    {
        return $this->hasMany(Affiliate::class, 'program_id', 'program_id');
    }

    /**
     * Get the referrals generated through this program.
     */
    public function referrals(): HasMany
    {
        return $this->hasMany(AffiliateReferral::class, 'program_id', 'program_id');
    }

    /**
     * Get the payouts made for this program.
     */
    public function payouts(): HasMany
    {
        return $this->hasMany(AffiliatePayout::class, 'program_id', 'program_id');
    }

    /**
     * Toggle the active status of the program.
     *
     * @return $this
     */
    public function toggleActive()
    {
        $this->update(['is_active' => !$this->is_active]);
        return $this;
    }

    /**
     * Check if a course is eligible for this program.
     *
     * @param int $courseId
     * @return bool
     */
    public function isCourseEligible($courseId)
    {
        if ($this->apply_to_all_courses) {
            return true;
        }

        if (!is_array($this->eligible_courses)) {
            return false;
        }

        return in_array($courseId, $this->eligible_courses);
    }

    /**
     * Calculate commission for a given amount.
     *
     * @param float $amount
     * @return float
     */
    public function calculateCommission($amount)
    {
        if ($this->commission_type === self::TYPE_PERCENTAGE) {
            return ($amount * $this->commission_rate) / 100;
        }

        return $this->commission_rate;
    }

    /**
     * Add a course to eligible courses.
     *
     * @param int $courseId
     * @return $this
     */
    public function addEligibleCourse($courseId)
    {
        if ($this->apply_to_all_courses) {
            return $this;
        }

        $courses = $this->eligible_courses ?? [];
        
        if (!in_array($courseId, $courses)) {
            $courses[] = $courseId;
            $this->update(['eligible_courses' => $courses]);
        }

        return $this;
    }

    /**
     * Remove a course from eligible courses.
     *
     * @param int $courseId
     * @return $this
     */
    public function removeEligibleCourse($courseId)
    {
        if ($this->apply_to_all_courses) {
            return $this;
        }

        $courses = $this->eligible_courses ?? [];
        
        if (($key = array_search($courseId, $courses)) !== false) {
            unset($courses[$key]);
            $this->update(['eligible_courses' => array_values($courses)]);
        }

        return $this;
    }
    
    /**
     * Check if a domain is approved for this program.
     *
     * @param string $domain
     * @return bool
     */
    public function isDomainApproved($domain)
    {
        if (empty($this->approved_domains)) {
            return true; // No restrictions
        }
        
        if (in_array($domain, $this->banned_domains ?? [])) {
            return false;
        }
        
        return in_array($domain, $this->approved_domains);
    }
    
    /**
     * Get total earnings generated by this program.
     *
     * @return float
     */
    public function getTotalEarnings()
    {
        return $this->referrals()
                   ->where('status', AffiliateReferral::STATUS_COMPLETED)
                   ->sum('commission_amount');
    }
    
    /**
     * Get total number of sales generated by this program.
     *
     * @return int
     */
    public function getTotalSales()
    {
        return $this->referrals()
                   ->where('status', AffiliateReferral::STATUS_COMPLETED)
                   ->count();
    }
    
    /**
     * Get total number of clicks generated by this program.
     *
     * @return int
     */
    public function getTotalClicks()
    {
        return $this->referrals()->sum('clicks');
    }
    
    /**
     * Get the conversion rate for this program.
     *
     * @return float
     */
    public function getConversionRate()
    {
        $clicks = $this->getTotalClicks();
        
        if ($clicks == 0) {
            return 0;
        }
        
        $sales = $this->getTotalSales();
        
        return ($sales / $clicks) * 100;
    }

    /**
     * Scope a query to only include active programs.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to only include inactive programs.
     */
    public function scopeInactive($query)
    {
        return $query->where('is_active', false);
    }
}
