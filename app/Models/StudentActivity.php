<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentActivity extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'student_id',
        'activity_type',
        'title',
        'description',
        'related_entity',
        'related_id'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Get the student who performed the activity.
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id', 'user_id');
    }

    /**
     * Record a login activity.
     */
    public static function recordLogin($studentId)
    {
        return self::create([
            'student_id' => $studentId,
            'activity_type' => 'login',
            'title' => 'تسجيل الدخول',
            'description' => 'قام الطالب بتسجيل الدخول إلى المنصة'
        ]);
    }

    /**
     * Record a course enrollment activity.
     */
    public static function recordCourseEnrollment($studentId, $courseId, $courseTitle)
    {
        return self::create([
            'student_id' => $studentId,
            'activity_type' => 'course',
            'title' => 'التسجيل في دورة',
            'description' => "قام الطالب بالتسجيل في دورة: {$courseTitle}",
            'related_entity' => 'courses',
            'related_id' => $courseId
        ]);
    }

    /**
     * Record a video watch activity.
     */
    public static function recordVideoWatch($studentId, $videoId, $videoTitle, $courseId = null)
    {
        return self::create([
            'student_id' => $studentId,
            'activity_type' => 'video_watch',
            'title' => 'مشاهدة فيديو',
            'description' => "قام الطالب بمشاهدة فيديو: {$videoTitle}",
            'related_entity' => 'videos',
            'related_id' => $videoId
        ]);
    }

    /**
     * Record an exam attempt activity.
     */
    public static function recordExamAttempt($studentId, $examId, $examTitle, $score = null)
    {
        $description = "قام الطالب بمحاولة اختبار: {$examTitle}";
        
        if ($score !== null) {
            $description .= " وحصل على درجة {$score}%";
        }
        
        return self::create([
            'student_id' => $studentId,
            'activity_type' => 'exam',
            'title' => 'محاولة اختبار',
            'description' => $description,
            'related_entity' => 'exams',
            'related_id' => $examId
        ]);
    }

    /**
     * Record a quiz attempt activity.
     */
    public static function recordQuizAttempt($studentId, $quizId, $quizTitle, $score = null)
    {
        $description = "قام الطالب بمحاولة اختبار قصير: {$quizTitle}";
        
        if ($score !== null) {
            $description .= " وحصل على درجة {$score}%";
        }
        
        return self::create([
            'student_id' => $studentId,
            'activity_type' => 'quiz',
            'title' => 'محاولة اختبار قصير',
            'description' => $description,
            'related_entity' => 'quizzes',
            'related_id' => $quizId
        ]);
    }

    /**
     * Record a course completion activity.
     */
    public static function recordCourseCompletion($studentId, $courseId, $courseTitle)
    {
        return self::create([
            'student_id' => $studentId,
            'activity_type' => 'course_completion',
            'title' => 'إكمال دورة',
            'description' => "أكمل الطالب دورة: {$courseTitle} بنجاح",
            'related_entity' => 'courses',
            'related_id' => $courseId
        ]);
    }

    /**
     * Record a certificate earned activity.
     */
    public static function recordCertificateEarned($studentId, $certificateId, $courseTitle)
    {
        return self::create([
            'student_id' => $studentId,
            'activity_type' => 'certificate',
            'title' => 'الحصول على شهادة',
            'description' => "حصل الطالب على شهادة إكمال دورة: {$courseTitle}",
            'related_entity' => 'certificates',
            'related_id' => $certificateId
        ]);
    }

    /**
     * Record a custom activity.
     */
    public static function recordCustomActivity($studentId, $type, $title, $description, $relatedEntity = null, $relatedId = null)
    {
        return self::create([
            'student_id' => $studentId,
            'activity_type' => $type,
            'title' => $title,
            'description' => $description,
            'related_entity' => $relatedEntity,
            'related_id' => $relatedId
        ]);
    }
} 