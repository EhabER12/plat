<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class EmailTemplate extends Model
{
    use HasFactory;
    
    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'template_id';
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'slug',
        'subject',
        'body',
        'description',
        'variables',
        'default_sender_name',
        'default_sender_email',
        'default_cc',
        'default_bcc',
        'is_active',
        'category',
        'created_by',
        'updated_by',
    ];
    
    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'variables' => 'json',
        'default_cc' => 'array',
        'default_bcc' => 'array',
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
    
    /**
     * Common email template categories.
     */
    const CATEGORY_USER = 'user';
    const CATEGORY_COURSE = 'course';
    const CATEGORY_PAYMENT = 'payment';
    const CATEGORY_NOTIFICATION = 'notification';
    const CATEGORY_SYSTEM = 'system';
    const CATEGORY_MARKETING = 'marketing';
    
    /**
     * Common template slugs.
     */
    const WELCOME_EMAIL = 'welcome-email';
    const PASSWORD_RESET = 'password-reset';
    const EMAIL_VERIFICATION = 'email-verification';
    const COURSE_ENROLLMENT = 'course-enrollment';
    const COURSE_COMPLETION = 'course-completion';
    const PAYMENT_RECEIPT = 'payment-receipt';
    const REFUND_NOTIFICATION = 'refund-notification';
    const INSTRUCTOR_APPLICATION_APPROVED = 'instructor-application-approved';
    const INSTRUCTOR_APPLICATION_REJECTED = 'instructor-application-rejected';
    
    /**
     * Get the creator of this template.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
    
    /**
     * Get the last updater of this template.
     */
    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
    
    /**
     * Get email logs related to this template.
     */
    public function emailLogs()
    {
        return $this->hasMany(EmailLog::class, 'template_id');
    }
    
    /**
     * Format the template with given data.
     *
     * @param array $data
     * @return array
     */
    public function format(array $data)
    {
        try {
            $subject = $this->subject;
            $body = $this->body;
            
            // Replace placeholders in subject and body
            foreach ($data as $key => $value) {
                if (is_string($value) || is_numeric($value)) {
                    $subject = str_replace('{{' . $key . '}}', $value, $subject);
                    $body = str_replace('{{' . $key . '}}', $value, $body);
                }
            }
            
            return [
                'subject' => $subject,
                'body' => $body,
                'sender_name' => $data['sender_name'] ?? $this->default_sender_name,
                'sender_email' => $data['sender_email'] ?? $this->default_sender_email,
                'cc' => $data['cc'] ?? $this->default_cc,
                'bcc' => $data['bcc'] ?? $this->default_bcc,
            ];
        } catch (\Exception $e) {
            Log::error('Error formatting email template: ' . $e->getMessage(), [
                'template_id' => $this->template_id,
                'template_name' => $this->name,
                'data' => $data,
            ]);
            
            return [
                'subject' => $this->subject,
                'body' => $this->body,
                'sender_name' => $this->default_sender_name,
                'sender_email' => $this->default_sender_email,
                'cc' => $this->default_cc,
                'bcc' => $this->default_bcc,
            ];
        }
    }
    
    /**
     * Validate template variables against provided data.
     *
     * @param array $data
     * @return array
     */
    public function validateVariables(array $data)
    {
        $missingVariables = [];
        $templateVariables = $this->variables ?? [];
        
        foreach ($templateVariables as $variable) {
            if (!isset($data[$variable]) && !array_key_exists($variable, $data)) {
                $missingVariables[] = $variable;
            }
        }
        
        return $missingVariables;
    }
    
    /**
     * Find template by slug.
     *
     * @param string $slug
     * @return EmailTemplate|null
     */
    public static function findBySlug($slug)
    {
        return self::where('slug', $slug)->where('is_active', true)->first();
    }
    
    /**
     * Scope a query to only include active templates.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
    
    /**
     * Scope a query to filter templates by category.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $category
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeInCategory($query, $category)
    {
        return $query->where('category', $category);
    }
}
