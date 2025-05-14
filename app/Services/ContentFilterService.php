<?php

namespace App\Services;

use App\Models\BannedWord;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class ContentFilterService
{
    /**
     * Cache key for banned words
     * 
     * @var string
     */
    protected const BANNED_WORDS_CACHE_KEY = 'banned_words';

    /**
     * Cache expiration time in seconds (24 hours)
     * 
     * @var int
     */
    protected const CACHE_EXPIRATION = 86400;

    /**
     * @var AdminNotificationService
     */
    protected $notificationService;

    /**
     * ContentFilterService constructor.
     */
    public function __construct()
    {
        // نستخدم الحقن اللازم عند الحاجة فقط لتجنب التبعيات الدائرية
        $this->notificationService = App::make(AdminNotificationService::class);
    }

    /**
     * Filter content based on banned words
     *
     * @param string $content Content to filter
     * @param array $options Optional configuration
     * @return array Filtered content and metadata
     */
    public function filterContent(string $content, array $options = []): array
    {
        $defaultOptions = [
            'minSeverity' => 1,
            'types' => ['general', 'contact_info', 'profanity'],
            'returnOriginalContent' => true,
            'returnFilteredContent' => true,
            'returnFoundWords' => true,
            'returnHighestSeverity' => true,
        ];

        $options = array_merge($defaultOptions, $options);
        
        // Get all active banned words from cache or database
        $bannedWords = $this->getBannedWords($options['minSeverity'], $options['types']);
        
        // Track found banned words and their details
        $foundWords = [];
        $highestSeverity = 0;
        $filteredContent = $content;
        
        foreach ($bannedWords as $bannedWord) {
            // Case-insensitive word boundary search
            $pattern = '/\b' . preg_quote($bannedWord->word, '/') . '\b/iu';
            
            if (preg_match($pattern, $content)) {
                // Word found
                $foundWords[] = [
                    'word' => $bannedWord->word,
                    'type' => $bannedWord->type,
                    'severity' => $bannedWord->severity,
                ];
                
                // Track highest severity level
                if ($bannedWord->severity > $highestSeverity) {
                    $highestSeverity = $bannedWord->severity;
                }
                
                // Replace word if needed
                if ($options['returnFilteredContent']) {
                    $replacement = $bannedWord->replacement ?? str_repeat('*', mb_strlen($bannedWord->word));
                    $filteredContent = preg_replace($pattern, $replacement, $filteredContent);
                }
            }
        }
        
        // Prepare result
        $result = [
            'has_banned_content' => !empty($foundWords),
        ];
        
        if ($options['returnOriginalContent']) {
            $result['original_content'] = $content;
        }
        
        if ($options['returnFilteredContent']) {
            $result['filtered_content'] = $filteredContent;
        }
        
        if ($options['returnFoundWords']) {
            $result['found_words'] = $foundWords;
        }
        
        if ($options['returnHighestSeverity']) {
            $result['highest_severity'] = $highestSeverity;
        }
        
        return $result;
    }
    
    /**
     * Check if content contains banned words
     *
     * @param string $content Content to check
     * @param int $minSeverity Minimum severity level
     * @param array $types Types of banned words to check
     * @return bool
     */
    public function containsBannedContent(string $content, int $minSeverity = 1, array $types = ['general']): bool
    {
        $result = $this->filterContent($content, [
            'minSeverity' => $minSeverity,
            'types' => $types,
            'returnOriginalContent' => false,
            'returnFilteredContent' => false,
            'returnHighestSeverity' => false,
        ]);
        
        return $result['has_banned_content'];
    }
    
    /**
     * Process message content and notify admins if banned content is found
     *
     * @param string $content المحتوى الأصلي للرسالة
     * @param User $user المستخدم المرسل للرسالة
     * @param Model $messageModel نموذج الرسالة
     * @param bool $notifyAdmin هل يجب إرسال إشعار للمشرف؟
     * @return array نتائج تصفية المحتوى
     */
    public function processMessageContent(string $content, User $user, Model $messageModel, bool $notifyAdmin = true): array
    {
        // Log the incoming message content for analysis
        Log::info('Processing message content for banned words', [
            'user_id' => $user->user_id,
            'user_name' => $user->name,
            'user_type' => $user->hasRole('instructor') ? 'instructor' : 'student',
            'message_id' => $messageModel->getKey(),
            'content_length' => strlen($content),
            'notify_admin' => $notifyAdmin
        ]);
        
        // فلترة المحتوى
        $filterResult = $this->filterContent($content, [
            'minSeverity' => 1,
            'types' => ['general', 'contact_info', 'profanity'],
        ]);
        
        // Log the filter results
        Log::info('Content filter results', [
            'user_id' => $user->user_id,
            'message_id' => $messageModel->getKey(),
            'has_banned_content' => $filterResult['has_banned_content'],
            'found_words_count' => count($filterResult['found_words'] ?? []),
            'highest_severity' => $filterResult['highest_severity'] ?? 0
        ]);
        
        // إرسال إشعار للمشرفين عند اكتشاف محتوى محظور
        if ($notifyAdmin && $filterResult['has_banned_content']) {
            $notification = $this->notifyAdminAboutBannedContent(
                $user,
                $messageModel,
                $filterResult['found_words'],
                $filterResult['highest_severity']
            );
            
            // Log notification creation
            Log::warning('Admin notification created for banned content', [
                'notification_id' => $notification->id,
                'user_id' => $user->user_id,
                'message_id' => $messageModel->getKey(),
                'found_words' => array_column($filterResult['found_words'], 'word'),
                'severity' => $filterResult['highest_severity']
            ]);
        }
        
        return $filterResult;
    }
    
    /**
     * إرسال إشعار للمشرفين عن المحتوى المحظور
     *
     * @param User $user المستخدم
     * @param Model $message الرسالة
     * @param array $foundWords الكلمات المحظورة التي تم اكتشافها
     * @param int $severity مستوى الخطورة
     * @return AdminNotification The created notification
     */
    protected function notifyAdminAboutBannedContent(User $user, Model $message, array $foundWords, int $severity)
    {
        // استخراج الكلمات المحظورة فقط من مصفوفة النتائج
        $bannedWordsList = array_column($foundWords, 'word');
        
        // إرسال إشعار باستخدام خدمة الإشعارات
        return $this->notificationService->createFlaggedContentNotification(
            $user,
            $message,
            $bannedWordsList,
            $severity
        );
    }
    
    /**
     * Get banned words from cache or database
     *
     * @param int $minSeverity Minimum severity level
     * @param array $types Types of banned words to get
     * @return \Illuminate\Database\Eloquent\Collection
     */
    protected function getBannedWords(int $minSeverity = 1, array $types = ['general'])
    {
        $cacheKey = self::BANNED_WORDS_CACHE_KEY . ':' . $minSeverity . ':' . implode('-', $types);
        
        return Cache::remember($cacheKey, self::CACHE_EXPIRATION, function () use ($minSeverity, $types) {
            return BannedWord::active()
                ->whereIn('type', $types)
                ->where('severity', '>=', $minSeverity)
                ->get();
        });
    }
    
    /**
     * Clear banned words cache
     *
     * @return void
     */
    public function clearBannedWordsCache(): void
    {
        Cache::forget(self::BANNED_WORDS_CACHE_KEY);
    }
} 