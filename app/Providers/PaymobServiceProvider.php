<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class PaymobServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__.'/../../config/paymob.php', 'paymob'
        );
    }

    public function boot()
    {
        $this->publishes([
            __DIR__.'/../../config/paymob.php' => config_path('paymob.php'),
        ], 'config');
    }
} 