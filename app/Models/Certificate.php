<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Certificate extends Model
{
    use HasFactory;

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'certificate_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'course_id',
        'completion_date',
        'certificate_number',
        'certificate_url',
        'is_valid'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'completion_date' => 'datetime',
        'is_valid' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Get the user that owns the certificate.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    /**
     * Get the course this certificate is for.
     */
    public function course()
    {
        return $this->belongsTo(Course::class, 'course_id', 'course_id');
    }

    /**
     * Check if the certificate is valid.
     */
    public function isValid()
    {
        if ($this->status !== 'issued') {
            return false;
        }

        if ($this->expiry_date && now()->gt($this->expiry_date)) {
            return false;
        }

        return true;
    }

    /**
     * Generate a unique certificate number.
     */
    public static function generateCertificateNumber()
    {
        $prefix = 'CERT';
        $timestamp = now()->format('YmdHis');
        $random = rand(1000, 9999);
        
        return $prefix . '-' . $timestamp . '-' . $random;
    }

    /**
     * Generate a verification code for the certificate.
     */
    public static function generateVerificationCode()
    {
        return strtoupper(substr(md5(uniqid(rand(), true)), 0, 8));
    }

    /**
     * Verify a certificate using its verification code.
     */
    public static function verify($certificateNumber, $verificationCode)
    {
        $certificate = self::where('certificate_number', $certificateNumber)
                           ->where('verification_code', $verificationCode)
                           ->first();

        return $certificate && $certificate->isValid();
    }
}
