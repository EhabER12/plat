<?php

namespace App\Providers;

use App\Services\StudentMotivationService;
use App\Services\NotificationService;
use Illuminate\Support\ServiceProvider;

class MotivationServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // تسجيل خدمة التحفيز للطلاب
        $this->app->singleton(StudentMotivationService::class, function ($app) {
            return new StudentMotivationService();
        });

        // تسجيل خدمة الإشعارات
        $this->app->singleton(NotificationService::class, function ($app) {
            return new NotificationService();
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
