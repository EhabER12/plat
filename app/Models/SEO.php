<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class SEO extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'seo_metadata';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'seo_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'entity_type',
        'entity_id',
        'slug',
        'url',
        'title',
        'description',
        'keywords',
        'canonical_url',
        'robots',
        'og_title',
        'og_description',
        'og_image',
        'twitter_title',
        'twitter_description',
        'twitter_image',
        'twitter_card',
        'schema_markup',
        'structured_data',
        'focus_keyword',
        'score',
        'is_indexed',
        'is_followed',
        'language',
        'locale',
        'custom_head',
        'redirect_type',
        'redirect_url',
        'last_analyzed',
        'analysis_results'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'keywords' => 'array',
        'schema_markup' => 'array',
        'structured_data' => 'array',
        'score' => 'integer',
        'is_indexed' => 'boolean',
        'is_followed' => 'boolean',
        'last_analyzed' => 'datetime',
        'analysis_results' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Get the parent model (Post, Page, Course, etc).
     */
    public function entity(): MorphTo
    {
        return $this->morphTo('entity');
    }

    /**
     * Generate robots meta tag content.
     *
     * @return string
     */
    public function getRobotsContent()
    {
        $directives = [];
        
        if (!$this->is_indexed) {
            $directives[] = 'noindex';
        } else {
            $directives[] = 'index';
        }
        
        if (!$this->is_followed) {
            $directives[] = 'nofollow';
        } else {
            $directives[] = 'follow';
        }
        
        if (!empty($this->robots)) {
            $customDirectives = explode(',', $this->robots);
            foreach ($customDirectives as $directive) {
                $directive = trim($directive);
                if (!empty($directive) && !in_array($directive, $directives)) {
                    $directives[] = $directive;
                }
            }
        }
        
        return implode(', ', $directives);
    }

    /**
     * Get the effective title.
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title ?? optional($this->entity)->title ?? '';
    }

    /**
     * Get the effective description.
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description ?? optional($this->entity)->description ?? '';
    }

    /**
     * Get the effective keywords.
     *
     * @param bool $asString
     * @return array|string
     */
    public function getKeywords($asString = false)
    {
        $keywords = $this->keywords ?? [];
        
        if ($asString) {
            return implode(', ', $keywords);
        }
        
        return $keywords;
    }

    /**
     * Get the effective canonical URL.
     *
     * @return string
     */
    public function getCanonicalUrl()
    {
        return $this->canonical_url ?? $this->url ?? '';
    }

    /**
     * Get the OG title.
     *
     * @return string
     */
    public function getOgTitle()
    {
        return $this->og_title ?? $this->getTitle();
    }

    /**
     * Get the OG description.
     *
     * @return string
     */
    public function getOgDescription()
    {
        return $this->og_description ?? $this->getDescription();
    }

    /**
     * Get the OG image URL.
     *
     * @return string|null
     */
    public function getOgImageUrl()
    {
        if (empty($this->og_image)) {
            // Try to get image from entity
            $entity = $this->entity;
            if ($entity && method_exists($entity, 'getImageUrl')) {
                return $entity->getImageUrl();
            }
            
            return null;
        }
        
        // Check if the image is a media library ID
        $media = MediaLibrary::find($this->og_image);
        if ($media) {
            return $media->url;
        }
        
        // If it's a URL, return it
        if (filter_var($this->og_image, FILTER_VALIDATE_URL)) {
            return $this->og_image;
        }
        
        // Assume it's a path in the storage
        return asset('storage/' . $this->og_image);
    }

    /**
     * Get the Twitter title.
     *
     * @return string
     */
    public function getTwitterTitle()
    {
        return $this->twitter_title ?? $this->getOgTitle();
    }

    /**
     * Get the Twitter description.
     *
     * @return string
     */
    public function getTwitterDescription()
    {
        return $this->twitter_description ?? $this->getOgDescription();
    }

    /**
     * Get the Twitter image URL.
     *
     * @return string|null
     */
    public function getTwitterImageUrl()
    {
        if (empty($this->twitter_image)) {
            return $this->getOgImageUrl();
        }
        
        // Check if the image is a media library ID
        $media = MediaLibrary::find($this->twitter_image);
        if ($media) {
            return $media->url;
        }
        
        // If it's a URL, return it
        if (filter_var($this->twitter_image, FILTER_VALIDATE_URL)) {
            return $this->twitter_image;
        }
        
        // Assume it's a path in the storage
        return asset('storage/' . $this->twitter_image);
    }

    /**
     * Get the Twitter card type.
     *
     * @return string
     */
    public function getTwitterCard()
    {
        return $this->twitter_card ?? 'summary_large_image';
    }

    /**
     * Generate a complete HTML meta tag for the title.
     *
     * @return string
     */
    public function getTitleTag()
    {
        $title = $this->getTitle();
        return '<title>' . e($title) . '</title>';
    }

    /**
     * Generate a complete HTML meta tag for the description.
     *
     * @return string
     */
    public function getDescriptionTag()
    {
        $description = $this->getDescription();
        if (empty($description)) {
            return '';
        }
        
        return '<meta name="description" content="' . e($description) . '">';
    }

    /**
     * Generate a complete HTML meta tag for the keywords.
     *
     * @return string
     */
    public function getKeywordsTag()
    {
        $keywords = $this->getKeywords(true);
        if (empty($keywords)) {
            return '';
        }
        
        return '<meta name="keywords" content="' . e($keywords) . '">';
    }

    /**
     * Generate a complete HTML meta tag for robots.
     *
     * @return string
     */
    public function getRobotsTag()
    {
        $robots = $this->getRobotsContent();
        return '<meta name="robots" content="' . e($robots) . '">';
    }

    /**
     * Generate a complete HTML link tag for the canonical URL.
     *
     * @return string
     */
    public function getCanonicalTag()
    {
        $url = $this->getCanonicalUrl();
        if (empty($url)) {
            return '';
        }
        
        return '<link rel="canonical" href="' . e($url) . '">';
    }

    /**
     * Generate complete HTML meta tags for OpenGraph.
     *
     * @return string
     */
    public function getOpenGraphTags()
    {
        $tags = [];
        
        // Basic tags
        $tags[] = '<meta property="og:type" content="' . ($this->entity_type == 'article' ? 'article' : 'website') . '">';
        $tags[] = '<meta property="og:title" content="' . e($this->getOgTitle()) . '">';
        
        $description = $this->getOgDescription();
        if (!empty($description)) {
            $tags[] = '<meta property="og:description" content="' . e($description) . '">';
        }
        
        $url = $this->getCanonicalUrl();
        if (!empty($url)) {
            $tags[] = '<meta property="og:url" content="' . e($url) . '">';
        }
        
        $image = $this->getOgImageUrl();
        if (!empty($image)) {
            $tags[] = '<meta property="og:image" content="' . e($image) . '">';
        }
        
        // Add locale if present
        if (!empty($this->locale)) {
            $tags[] = '<meta property="og:locale" content="' . e($this->locale) . '">';
        }
        
        return implode("\n", $tags);
    }

    /**
     * Generate complete HTML meta tags for Twitter.
     *
     * @return string
     */
    public function getTwitterTags()
    {
        $tags = [];
        
        $tags[] = '<meta name="twitter:card" content="' . e($this->getTwitterCard()) . '">';
        $tags[] = '<meta name="twitter:title" content="' . e($this->getTwitterTitle()) . '">';
        
        $description = $this->getTwitterDescription();
        if (!empty($description)) {
            $tags[] = '<meta name="twitter:description" content="' . e($description) . '">';
        }
        
        $image = $this->getTwitterImageUrl();
        if (!empty($image)) {
            $tags[] = '<meta name="twitter:image" content="' . e($image) . '">';
        }
        
        return implode("\n", $tags);
    }

    /**
     * Get all the schema markup as a JSON-LD script tag.
     *
     * @return string
     */
    public function getSchemaMarkupTag()
    {
        $markup = $this->schema_markup;
        if (empty($markup)) {
            return '';
        }
        
        if (is_array($markup)) {
            $json = json_encode($markup);
        } elseif (is_string($markup) && json_decode($markup) !== null) {
            $json = $markup;
        } else {
            return '';
        }
        
        return '<script type="application/ld+json">' . $json . '</script>';
    }

    /**
     * Get all the structured data.
     *
     * @return string
     */
    public function getStructuredDataTag()
    {
        $data = $this->structured_data;
        if (empty($data)) {
            return '';
        }
        
        if (is_array($data)) {
            $json = json_encode($data);
        } elseif (is_string($data) && json_decode($data) !== null) {
            $json = $data;
        } else {
            return '';
        }
        
        return '<script type="application/ld+json">' . $json . '</script>';
    }

    /**
     * Get all HTML head elements combined.
     *
     * @return string
     */
    public function getAllHeadElements()
    {
        $elements = [];
        
        $elements[] = $this->getTitleTag();
        $elements[] = $this->getDescriptionTag();
        $elements[] = $this->getKeywordsTag();
        $elements[] = $this->getRobotsTag();
        $elements[] = $this->getCanonicalTag();
        $elements[] = $this->getOpenGraphTags();
        $elements[] = $this->getTwitterTags();
        $elements[] = $this->getSchemaMarkupTag();
        $elements[] = $this->getStructuredDataTag();
        
        // Add custom head content
        if (!empty($this->custom_head)) {
            $elements[] = $this->custom_head;
        }
        
        // Filter out empty elements
        $elements = array_filter($elements);
        
        return implode("\n", $elements);
    }

    /**
     * Analyze SEO and calculate score.
     *
     * @return int Score between 0-100
     */
    public function analyze()
    {
        $score = 0;
        $results = [];
        
        // Title analysis
        $title = $this->getTitle();
        $titleLength = mb_strlen($title);
        
        if (!empty($title)) {
            if ($titleLength >= 40 && $titleLength <= 60) {
                $score += 10;
                $results['title'] = [
                    'status' => 'good',
                    'message' => 'Title length is optimal ('.$titleLength.' characters)',
                ];
            } elseif ($titleLength > 0 && $titleLength < 40) {
                $score += 5;
                $results['title'] = [
                    'status' => 'warning',
                    'message' => 'Title is too short ('.$titleLength.' characters)',
                ];
            } elseif ($titleLength > 60) {
                $score += 3;
                $results['title'] = [
                    'status' => 'warning',
                    'message' => 'Title is too long ('.$titleLength.' characters)',
                ];
            }
        } else {
            $results['title'] = [
                'status' => 'error',
                'message' => 'Missing title',
            ];
        }
        
        // Description analysis
        $description = $this->getDescription();
        $descriptionLength = mb_strlen($description);
        
        if (!empty($description)) {
            if ($descriptionLength >= 120 && $descriptionLength <= 160) {
                $score += 10;
                $results['description'] = [
                    'status' => 'good',
                    'message' => 'Description length is optimal ('.$descriptionLength.' characters)',
                ];
            } elseif ($descriptionLength > 0 && $descriptionLength < 120) {
                $score += 5;
                $results['description'] = [
                    'status' => 'warning',
                    'message' => 'Description is too short ('.$descriptionLength.' characters)',
                ];
            } elseif ($descriptionLength > 160) {
                $score += 3;
                $results['description'] = [
                    'status' => 'warning',
                    'message' => 'Description is too long ('.$descriptionLength.' characters)',
                ];
            }
        } else {
            $results['description'] = [
                'status' => 'error',
                'message' => 'Missing description',
            ];
        }
        
        // Keywords analysis
        $keywords = $this->getKeywords();
        
        if (!empty($keywords)) {
            $score += 5;
            $results['keywords'] = [
                'status' => 'good',
                'message' => 'Keywords are set ('.count($keywords).' keywords)',
            ];
            
            // Focus keyword in title and description
            if (!empty($this->focus_keyword)) {
                if (stripos($title, $this->focus_keyword) !== false) {
                    $score += 5;
                    $results['focus_keyword_title'] = [
                        'status' => 'good',
                        'message' => 'Focus keyword is present in the title',
                    ];
                } else {
                    $results['focus_keyword_title'] = [
                        'status' => 'warning',
                        'message' => 'Focus keyword is not present in the title',
                    ];
                }
                
                if (stripos($description, $this->focus_keyword) !== false) {
                    $score += 5;
                    $results['focus_keyword_description'] = [
                        'status' => 'good',
                        'message' => 'Focus keyword is present in the description',
                    ];
                } else {
                    $results['focus_keyword_description'] = [
                        'status' => 'warning',
                        'message' => 'Focus keyword is not present in the description',
                    ];
                }
            }
        } else {
            $results['keywords'] = [
                'status' => 'warning',
                'message' => 'Missing keywords',
            ];
        }
        
        // URL/Slug analysis
        if (!empty($this->slug)) {
            $score += 5;
            $results['slug'] = [
                'status' => 'good',
                'message' => 'URL slug is set',
            ];
            
            // Focus keyword in slug
            if (!empty($this->focus_keyword) && stripos($this->slug, $this->focus_keyword) !== false) {
                $score += 5;
                $results['focus_keyword_slug'] = [
                    'status' => 'good',
                    'message' => 'Focus keyword is present in the URL',
                ];
            }
        } else {
            $results['slug'] = [
                'status' => 'warning',
                'message' => 'Missing URL slug',
            ];
        }
        
        // OG and Twitter tags
        if (!empty($this->getOgImageUrl())) {
            $score += 5;
            $results['og_image'] = [
                'status' => 'good',
                'message' => 'Open Graph image is set',
            ];
        } else {
            $results['og_image'] = [
                'status' => 'warning',
                'message' => 'Missing Open Graph image',
            ];
        }
        
        // Schema markup
        if (!empty($this->schema_markup)) {
            $score += 10;
            $results['schema'] = [
                'status' => 'good',
                'message' => 'Schema markup is defined',
            ];
        } else {
            $results['schema'] = [
                'status' => 'warning',
                'message' => 'Missing schema markup',
            ];
        }
        
        // Canonical URL
        if (!empty($this->getCanonicalUrl())) {
            $score += 5;
            $results['canonical'] = [
                'status' => 'good',
                'message' => 'Canonical URL is set',
            ];
        } else {
            $results['canonical'] = [
                'status' => 'warning',
                'message' => 'Missing canonical URL',
            ];
        }
        
        // Robots index/follow
        if ($this->is_indexed && $this->is_followed) {
            $score += 5;
            $results['robots'] = [
                'status' => 'good',
                'message' => 'Page can be indexed and links can be followed',
            ];
        } elseif (!$this->is_indexed) {
            $results['robots'] = [
                'status' => 'warning',
                'message' => 'Page is set to noindex',
            ];
        } elseif (!$this->is_followed) {
            $results['robots'] = [
                'status' => 'warning',
                'message' => 'Page is set to nofollow',
            ];
        }
        
        // Redirects
        if (!empty($this->redirect_type) && !empty($this->redirect_url)) {
            $results['redirect'] = [
                'status' => 'warning',
                'message' => 'Page has a '.$this->redirect_type.' redirect',
            ];
        }
        
        // Update the model with results
        $this->update([
            'score' => min(100, $score),
            'analysis_results' => $results,
            'last_analyzed' => now(),
        ]);
        
        return $this->score;
    }

    /**
     * Scope a query to only include SEO entries for a specific entity.
     */
    public function scopeForEntity($query, $type, $id)
    {
        return $query->where('entity_type', $type)
                     ->where('entity_id', $id);
    }

    /**
     * Scope a query to only include SEO entries with a specific slug.
     */
    public function scopeBySlug($query, $slug)
    {
        return $query->where('slug', $slug);
    }

    /**
     * Scope a query to only include SEO entries by their score.
     */
    public function scopeWithMinScore($query, $score)
    {
        return $query->where('score', '>=', $score);
    }

    /**
     * Scope a query to only include SEO entries that need improvement.
     */
    public function scopeNeedsImprovement($query)
    {
        return $query->where('score', '<', 70);
    }

    /**
     * Scope a query to only include SEO entries with good scores.
     */
    public function scopeGoodScore($query)
    {
        return $query->where('score', '>=', 70);
    }

    /**
     * Scope a query to only include SEO entries that have redirects.
     */
    public function scopeWithRedirect($query)
    {
        return $query->whereNotNull('redirect_type')
                     ->whereNotNull('redirect_url');
    }

    /**
     * Scope a query to only include SEO entries in a specific language.
     */
    public function scopeInLanguage($query, $language)
    {
        return $query->where('language', $language);
    }

    /**
     * Create or update SEO data for an entity.
     *
     * @param string $entityType
     * @param int $entityId
     * @param array $data
     * @return SEO
     */
    public static function updateOrCreateForEntity($entityType, $entityId, array $data)
    {
        return self::updateOrCreate(
            [
                'entity_type' => $entityType,
                'entity_id' => $entityId,
            ],
            $data
        );
    }
}
