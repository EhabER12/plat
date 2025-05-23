<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Config;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
        $this->app->bind('parent', function ($app) {
            return $app->make(\App\Models\User::class);
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // تعطيل Pusher واستخدام مزود null بدلاً منه
        Config::set('broadcasting.default', 'null');

        // تأكد من استخدام قالب Bootstrap-4 للترقيم
        \Illuminate\Pagination\Paginator::useBootstrap();
    }
}
