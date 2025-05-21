<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InstructorVerification extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'instructor_verifications';

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
        'user_id',
        'education',
        'expertise',
        'years_of_experience',
        'linkedin_profile',
        'additional_info',
        'qualifications',
        'status',
        'submitted_at',
        'payment_details',
        'certificate_file',
        'id_document',
        'cv_document',
        'certificate_document',
        'admin_notes',
        'verified_by',
        'verified_at',
        'reviewed_at',
        'identification_type',
        'identification_number',
        'identification_image',
        'cv_file',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'payment_details' => 'array',
        'submitted_at' => 'datetime',
        'verified_at' => 'datetime',
        'reviewed_at' => 'datetime',
    ];

    /**
     * Get the user that owns the verification.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    /**
     * Get the admin that verified the request.
     */
    public function verifier()
    {
        return $this->belongsTo(User::class, 'verified_by', 'user_id');
    }
}
