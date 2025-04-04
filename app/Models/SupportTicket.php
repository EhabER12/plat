<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SupportTicket extends Model
{
    use HasFactory;

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'ticket_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'subject',
        'description',
        'status',
        'priority',
        'assigned_to',
        'resolved_at',
        'closed_at'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'resolved_at' => 'datetime',
        'closed_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Get the user who created the ticket.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    /**
     * Get the staff member assigned to the ticket.
     */
    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to', 'user_id');
    }

    /**
     * Get the responses to this ticket.
     */
    public function responses(): HasMany
    {
        return $this->hasMany(TicketResponse::class, 'ticket_id', 'ticket_id');
    }

    /**
     * Check if the ticket is open.
     */
    public function isOpen()
    {
        return $this->status != 'closed' && $this->closed_at === null;
    }

    /**
     * Check if the ticket is resolved.
     */
    public function isResolved()
    {
        return $this->status == 'resolved' || $this->resolved_at !== null;
    }

    /**
     * Assign the ticket to a staff member.
     */
    public function assignTo($userId)
    {
        $this->update([
            'assigned_to' => $userId,
            'status' => 'assigned'
        ]);
        return $this;
    }

    /**
     * Mark the ticket as resolved.
     */
    public function resolve()
    {
        $this->update([
            'status' => 'resolved',
            'resolved_at' => now()
        ]);
        return $this;
    }

    /**
     * Close the ticket.
     */
    public function close()
    {
        $this->update([
            'status' => 'closed',
            'closed_at' => now()
        ]);
        return $this;
    }
}
