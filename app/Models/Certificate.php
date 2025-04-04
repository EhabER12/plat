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
        'student_id',
        'course_id',
        'certificate_number',
        'issue_date',
        'expiry_date',
        'certificate_url',
        'status',
        'verification_code'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'issue_date' => 'datetime',
        'expiry_date' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Get the student who earned the certificate.
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id', 'user_id');
    }

    /**
     * Get the course for which the certificate was issued.
     */
    public function course(): BelongsTo
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
