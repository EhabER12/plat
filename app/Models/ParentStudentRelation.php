<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ParentStudentRelation extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'parent_id',
        'student_id',
        'student_name',
        'relation_type',
        'birth_certificate',
        'parent_id_card',
        'additional_document',
        'notes',
        'verification_status',
        'verification_notes',
        'verified_at',
        'verified_by',
        'token'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'verified_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Get the parent who owns the relation.
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(User::class, 'parent_id', 'user_id');
    }

    /**
     * Get the student who owns the relation.
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id', 'user_id');
    }

    /**
     * Get the admin who verified the relation.
     */
    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by', 'user_id');
    }

    /**
     * Check if the relation is pending verification.
     */
    public function isPending()
    {
        return $this->verification_status === 'pending';
    }

    /**
     * Check if the relation is approved.
     */
    public function isApproved()
    {
        return $this->verification_status === 'approved';
    }

    /**
     * Check if the relation is rejected.
     */
    public function isRejected()
    {
        return $this->verification_status === 'rejected';
    }

    /**
     * Approve the relation.
     */
    public function approve($adminId, $notes = null)
    {
        $this->update([
            'verification_status' => 'approved',
            'verification_notes' => $notes,
            'verified_at' => now(),
            'verified_by' => $adminId
        ]);

        return $this;
    }

    /**
     * Reject the relation.
     */
    public function reject($adminId, $notes = null)
    {
        $this->update([
            'verification_status' => 'rejected',
            'verification_notes' => $notes,
            'verified_at' => now(),
            'verified_by' => $adminId
        ]);

        return $this;
    }
}
