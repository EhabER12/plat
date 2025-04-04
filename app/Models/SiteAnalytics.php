<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SiteAnalytics extends Model
{
    use HasFactory;

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'analytics_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'date',
        'page_views',
        'visitors',
        'unique_visitors',
        'new_users',
        'bounce_rate',
        'avg_session_duration',
        'top_pages',
        'top_referrers',
        'top_browsers',
        'top_devices',
        'top_countries',
        'top_search_terms',
        'conversion_rate',
        'total_revenue',
        'metadata'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'date' => 'date',
        'page_views' => 'integer',
        'visitors' => 'integer',
        'unique_visitors' => 'integer',
        'new_users' => 'integer',
        'bounce_rate' => 'float',
        'avg_session_duration' => 'float',
        'top_pages' => 'array',
        'top_referrers' => 'array',
        'top_browsers' => 'array',
        'top_devices' => 'array',
        'top_countries' => 'array',
        'top_search_terms' => 'array',
        'conversion_rate' => 'float',
        'total_revenue' => 'decimal:2',
        'metadata' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Record analytics data for a specific date.
     *
     * @param string $date
     * @param array $data
     * @return SiteAnalytics
     */
    public static function recordForDate($date, array $data)
    {
        $analytics = self::firstOrNew(['date' => $date]);
        
        // Merge existing metadata if present
        if (isset($data['metadata']) && $analytics->metadata) {
            $data['metadata'] = array_merge($analytics->metadata, $data['metadata']);
        }
        
        $analytics->fill($data);
        $analytics->save();
        
        return $analytics;
    }

    /**
     * Get analytics for a date range.
     *
     * @param string $startDate
     * @param string $endDate
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getRange($startDate, $endDate)
    {
        return self::whereBetween('date', [$startDate, $endDate])
                  ->orderBy('date', 'asc')
                  ->get();
    }

    /**
     * Get total page views for a date range.
     *
     * @param string $startDate
     * @param string $endDate
     * @return int
     */
    public static function getTotalPageViews($startDate, $endDate)
    {
        return self::whereBetween('date', [$startDate, $endDate])
                  ->sum('page_views');
    }

    /**
     * Get total visitors for a date range.
     *
     * @param string $startDate
     * @param string $endDate
     * @return int
     */
    public static function getTotalVisitors($startDate, $endDate)
    {
        return self::whereBetween('date', [$startDate, $endDate])
                  ->sum('visitors');
    }

    /**
     * Get average bounce rate for a date range.
     *
     * @param string $startDate
     * @param string $endDate
     * @return float
     */
    public static function getAverageBounceRate($startDate, $endDate)
    {
        $records = self::whereBetween('date', [$startDate, $endDate])->get();
        
        if ($records->isEmpty()) {
            return 0;
        }
        
        return $records->avg('bounce_rate');
    }

    /**
     * Get average session duration for a date range.
     *
     * @param string $startDate
     * @param string $endDate
     * @return float
     */
    public static function getAverageSessionDuration($startDate, $endDate)
    {
        $records = self::whereBetween('date', [$startDate, $endDate])->get();
        
        if ($records->isEmpty()) {
            return 0;
        }
        
        return $records->avg('avg_session_duration');
    }

    /**
     * Get total revenue for a date range.
     *
     * @param string $startDate
     * @param string $endDate
     * @return float
     */
    public static function getTotalRevenue($startDate, $endDate)
    {
        return self::whereBetween('date', [$startDate, $endDate])
                  ->sum('total_revenue');
    }

    /**
     * Get aggregated top pages for a date range.
     *
     * @param string $startDate
     * @param string $endDate
     * @param int $limit
     * @return array
     */
    public static function getTopPages($startDate, $endDate, $limit = 10)
    {
        $analytics = self::whereBetween('date', [$startDate, $endDate])->get();
        
        if ($analytics->isEmpty()) {
            return [];
        }
        
        $pagesData = [];
        
        foreach ($analytics as $record) {
            if (!is_array($record->top_pages)) {
                continue;
            }
            
            foreach ($record->top_pages as $page => $views) {
                if (!isset($pagesData[$page])) {
                    $pagesData[$page] = 0;
                }
                
                $pagesData[$page] += $views;
            }
        }
        
        arsort($pagesData);
        
        return array_slice($pagesData, 0, $limit, true);
    }

    /**
     * Get the growth rate compared to previous period.
     *
     * @param string $metric
     * @param string $currentStartDate
     * @param string $currentEndDate
     * @return float
     */
    public static function getGrowthRate($metric, $currentStartDate, $currentEndDate)
    {
        $currentPeriodDays = (strtotime($currentEndDate) - strtotime($currentStartDate)) / 86400;
        $previousStartDate = date('Y-m-d', strtotime($currentStartDate . " - {$currentPeriodDays} days"));
        $previousEndDate = date('Y-m-d', strtotime($currentEndDate . " - {$currentPeriodDays} days"));
        
        $currentValue = self::whereBetween('date', [$currentStartDate, $currentEndDate])->sum($metric);
        $previousValue = self::whereBetween('date', [$previousStartDate, $previousEndDate])->sum($metric);
        
        if ($previousValue == 0) {
            return 100; // If previous value was 0, consider it 100% growth
        }
        
        return (($currentValue - $previousValue) / $previousValue) * 100;
    }
}
