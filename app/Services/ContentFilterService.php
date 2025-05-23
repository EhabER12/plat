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
        try {
            // نستخدم الحقن اللازم عند الحاجة فقط لتجنب التبعيات الدائرية
            $this->notificationService = App::make(AdminNotificationService::class);
        } catch (\Exception $e) {
            // Log error but continue without notification service
            Log::error('Error initializing ContentFilterService: ' . $e->getMessage());
            $this->notificationService = null;
        }
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
        try {
            $defaultOptions = [
                'minSeverity' => 1,
                'types' => ['general', 'contact_info', 'profanity'],
                'returnOriginalContent' => true,
                'returnFilteredContent' => true,
                'returnFoundWords' => true,
                'returnHighestSeverity' => true,
                'detectPhoneNumbers' => true,
                'detectSocialMedia' => true,
            ];

            $options = array_merge($defaultOptions, $options);

            // Get all active banned words from cache or database
            $bannedWords = $this->getBannedWords($options['minSeverity'], $options['types']);

            // Track found banned words and their details
            $foundWords = [];
            $highestSeverity = 0;
            $filteredContent = $content;

            // Detectar y filtrar números de teléfono
            if ($options['detectPhoneNumbers']) {
                $filteredContent = $this->detectAndFilterPhoneNumbers($content, $filteredContent, $foundWords, $highestSeverity);
            }

            // Detectar y filtrar patrones de redes sociales
            if ($options['detectSocialMedia']) {
                $filteredContent = $this->detectAndFilterSocialMedia($content, $filteredContent, $foundWords, $highestSeverity);
            }

            foreach ($bannedWords as $bannedWord) {
                // Preparar la palabra para búsqueda avanzada
                $wordToSearch = preg_quote($bannedWord->word, '/');

                // Patrones de búsqueda:
                // 1. Búsqueda exacta con límites de palabra
                $exactPattern = '/\b' . $wordToSearch . '\b/iu';

                // 2. Búsqueda flexible que permite espacios entre caracteres
                $flexibleWord = implode('\s*', preg_split('//u', $wordToSearch, -1, PREG_SPLIT_NO_EMPTY));
                $flexiblePattern = '/\b' . $flexibleWord . '\b/iu';

                // 3. Búsqueda que ignora caracteres especiales
                $normalizedWord = preg_replace('/\s+/', '', $wordToSearch);
                $normalizedPattern = '/\b' . $normalizedWord . '\b/iu';

                // Verificar si alguno de los patrones coincide
                if (preg_match($exactPattern, $content) ||
                    preg_match($flexiblePattern, $content) ||
                    preg_match($normalizedPattern, preg_replace('/\s+/', '', $content))) {

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

                        // Reemplazar todas las variantes
                        $filteredContent = preg_replace($exactPattern, $replacement, $filteredContent);
                        $filteredContent = preg_replace($flexiblePattern, $replacement, $filteredContent);

                        // Para el patrón normalizado, necesitamos un enfoque diferente
                        // ya que estamos buscando en una versión modificada del contenido
                        if (preg_match($normalizedPattern, preg_replace('/\s+/', '', $content))) {
                            // Si encontramos una coincidencia normalizada, usamos el patrón exacto
                            // pero con límites más flexibles
                            $loosePattern = '/' . $wordToSearch . '/iu';
                            $filteredContent = preg_replace($loosePattern, $replacement, $filteredContent);
                        }
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
        } catch (\Exception $e) {
            // Si ocurre cualquier error, registrarlo y devolver un resultado seguro
            Log::error('Error en el filtrado de contenido: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            // Devolver un resultado seguro que no bloquee el envío del mensaje
            return [
                'has_banned_content' => false,
                'original_content' => $content,
                'filtered_content' => $content,
                'found_words' => [],
                'highest_severity' => 0
            ];
        }
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
        try {
            // Log the incoming message content for analysis
            Log::info('Processing message content for banned words', [
                'user_id' => $user->user_id,
                'user_name' => $user->name,
                'message_id' => $messageModel->getKey(),
                'content_length' => strlen($content)
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
            if ($notifyAdmin && ($filterResult['has_banned_content'] ?? false)) {
                try {
                    $notification = $this->notifyAdminAboutBannedContent(
                        $user,
                        $messageModel,
                        $filterResult['found_words'] ?? [],
                        $filterResult['highest_severity'] ?? 0
                    );

                    // Log notification creation
                    Log::warning('Admin notification created for banned content', [
                        'notification_id' => $notification->id ?? 'unknown',
                        'user_id' => $user->user_id,
                        'message_id' => $messageModel->getKey(),
                        'found_words' => array_column($filterResult['found_words'] ?? [], 'word'),
                        'severity' => $filterResult['highest_severity'] ?? 0
                    ]);
                } catch (\Exception $e) {
                    // Log notification error but don't prevent message from being sent
                    Log::error('Failed to send notification about banned content', [
                        'error' => $e->getMessage(),
                        'user_id' => $user->user_id,
                        'message_id' => $messageModel->getKey()
                    ]);
                }
            }

            return $filterResult;
        } catch (\Exception $e) {
            // Log error but return empty result to prevent message send failure
            Log::error('Error in processMessageContent', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_id' => $user->user_id,
                'message_id' => $messageModel->getKey()
            ]);

            // Return a safe default result
            return [
                'has_banned_content' => false,
                'original_content' => $content,
                'filtered_content' => $content,
                'found_words' => [],
                'highest_severity' => 0
            ];
        }
    }

    /**
     * إرسال إشعار للمشرفين عن المحتوى المحظور
     *
     * @param User $user المستخدم
     * @param Model $message الرسالة
     * @param array $foundWords الكلمات المحظورة التي تم اكتشافها
     * @param int $severity مستوى الخطورة
     * @return mixed The created notification or null if error
     */
    protected function notifyAdminAboutBannedContent(User $user, Model $message, array $foundWords, int $severity)
    {
        try {
            // استخراج الكلمات المحظورة فقط من مصفوفة النتائج
            $bannedWordsList = array_column($foundWords, 'word');

            // إرسال إشعار باستخدام خدمة الإشعارات
            return $this->notificationService->createFlaggedContentNotification(
                $user,
                $message,
                $bannedWordsList,
                $severity
            );
        } catch (\Exception $e) {
            // Log error but don't throw exception
            Log::error('Error in notifyAdminAboutBannedContent', [
                'error' => $e->getMessage(),
                'user_id' => $user->user_id,
                'message_id' => $message->getKey()
            ]);

            return null;
        }
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
            try {
                return BannedWord::active()
                    ->whereIn('type', $types)
                    ->where('severity', '>=', $minSeverity)
                    ->get();
            } catch (\Exception $e) {
                // Si hay un error (como que la tabla no existe), devolver una colección vacía
                Log::warning('Error al obtener palabras prohibidas: ' . $e->getMessage());
                return collect([]);
            }
        });
    }

    /**
     * Detectar y filtrar números de teléfono en el contenido
     *
     * @param string $originalContent Contenido original
     * @param string $filteredContent Contenido ya filtrado
     * @param array &$foundWords Array de palabras encontradas (se modifica por referencia)
     * @param int &$highestSeverity Nivel de severidad más alto (se modifica por referencia)
     * @return string Contenido filtrado
     */
    protected function detectAndFilterPhoneNumbers(string $originalContent, string $filteredContent, array &$foundWords, int &$highestSeverity): string
    {
        try {
            // Patrones para detectar números de teléfono en varios formatos
            $patterns = [
                // Formato egipcio: 01xxxxxxxxx (11 dígitos)
                '/\b01[0-9]{9}\b/',

                // Formato con espacios o guiones: 010 1234 5678, 010-1234-5678
                '/\b01[0-9][\s\-]?[0-9]{4}[\s\-]?[0-9]{4}\b/',

                // Formato internacional: +20xxxxxxxxxx
                '/\+20[0-9]{10}\b/',

                // Formato con código de país: 002xxxxxxxxxx
                '/\b002[0-9]{11}\b/',

                // Números genéricos de 8-12 dígitos (con posibles separadores)
                '/\b[0-9]{3}[\s\-]?[0-9]{3}[\s\-]?[0-9]{3,4}\b/'
            ];

            foreach ($patterns as $pattern) {
                if (preg_match_all($pattern, $originalContent, $matches)) {
                    foreach ($matches[0] as $match) {
                        // Agregar a la lista de palabras encontradas
                        $foundWords[] = [
                            'word' => $match,
                            'type' => 'contact_info',
                            'severity' => 2, // Severidad media-alta para números de teléfono
                        ];

                        // Actualizar severidad máxima
                        if (2 > $highestSeverity) {
                            $highestSeverity = 2;
                        }

                        // Reemplazar en el contenido filtrado
                        $replacement = str_repeat('*', strlen($match));
                        $filteredContent = str_replace($match, $replacement, $filteredContent);
                    }
                }
            }

            return $filteredContent;
        } catch (\Exception $e) {
            // Log error but return original filtered content
            Log::error('Error detecting phone numbers: ' . $e->getMessage());
            return $filteredContent;
        }
    }

    /**
     * Detectar y filtrar patrones de redes sociales en el contenido
     *
     * @param string $originalContent Contenido original
     * @param string $filteredContent Contenido ya filtrado
     * @param array &$foundWords Array de palabras encontradas (se modifica por referencia)
     * @param int &$highestSeverity Nivel de severidad más alto (se modifica por referencia)
     * @return string Contenido filtrado
     */
    protected function detectAndFilterSocialMedia(string $originalContent, string $filteredContent, array &$foundWords, int &$highestSeverity): string
    {
        try {
            // Patrones para detectar menciones de redes sociales
            $patterns = [
                // URLs de redes sociales
                '/(https?:\/\/)?(www\.)?(facebook|fb|instagram|insta|twitter|t\.me|telegram|snap|snapchat|tiktok)\.(com|me)\/[a-zA-Z0-9_\.\-\/]+/',

                // Nombres de usuario con @ para varias plataformas
                '/@[a-zA-Z0-9_\.]{3,30}/',

                // Patrones específicos de WhatsApp
                '/wa\.me\/[0-9]+/',
                '/chat\.whatsapp\.com\/[a-zA-Z0-9]+/',

                // Patrones de correo electrónico
                '/[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}/'
            ];

            foreach ($patterns as $pattern) {
                if (preg_match_all($pattern, $originalContent, $matches)) {
                    foreach ($matches[0] as $match) {
                        // Agregar a la lista de palabras encontradas
                        $foundWords[] = [
                            'word' => $match,
                            'type' => 'contact_info',
                            'severity' => 2, // Severidad media-alta para contactos en redes sociales
                        ];

                        // Actualizar severidad máxima
                        if (2 > $highestSeverity) {
                            $highestSeverity = 2;
                        }

                        // Reemplazar en el contenido filtrado
                        $replacement = str_repeat('*', strlen($match));
                        $filteredContent = str_replace($match, $replacement, $filteredContent);
                    }
                }
            }

            return $filteredContent;
        } catch (\Exception $e) {
            // Log error but return original filtered content
            Log::error('Error detecting social media patterns: ' . $e->getMessage());
            return $filteredContent;
        }
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