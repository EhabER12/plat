<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InstructorPaymentAccount extends Model
{
    use HasFactory;

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'account_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'instructor_id',
        'payment_provider',
        'provider_account_id',
        'account_name',
        'account_details',
        'is_active',
        'is_default',
        'notes'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'account_details' => 'array',
        'is_active' => 'boolean',
        'is_default' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Get the instructor who owns the payment account.
     */
    public function instructor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'instructor_id', 'user_id');
    }

    /**
     * Set this account as the default for the instructor.
     */
    public function setAsDefault()
    {
        // First, unset any existing default accounts for this instructor
        self::where('instructor_id', $this->instructor_id)
            ->where('account_id', '!=', $this->account_id)
            ->update(['is_default' => false]);
        
        // Then set this account as default
        $this->is_default = true;
        $this->save();
        
        return $this;
    }

    /**
     * Activate this account.
     */
    public function activate()
    {
        $this->is_active = true;
        $this->save();
        
        return $this;
    }

    /**
     * Deactivate this account.
     */
    public function deactivate()
    {
        // If this is the default account, we can't deactivate it
        if ($this->is_default) {
            return false;
        }
        
        $this->is_active = false;
        $this->save();
        
        return $this;
    }

    /**
     * Get the masked account details for display.
     */
    public function getMaskedDetailsAttribute()
    {
        $details = $this->account_details;
        
        // Implement masking logic based on the payment provider
        if ($this->payment_provider === 'paymob') {
            // Mask account details for Paymob
            if (isset($details['email'])) {
                $email = $details['email'];
                $atPos = strpos($email, '@');
                if ($atPos !== false) {
                    $username = substr($email, 0, $atPos);
                    $domain = substr($email, $atPos);
                    $maskedUsername = substr($username, 0, 2) . str_repeat('*', strlen($username) - 2);
                    $details['email'] = $maskedUsername . $domain;
                }
            }
            
            if (isset($details['phone'])) {
                $phone = $details['phone'];
                $details['phone'] = substr($phone, 0, 4) . str_repeat('*', strlen($phone) - 7) . substr($phone, -3);
            }
        }
        
        return $details;
    }
}
