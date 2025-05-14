<?php

namespace App\Providers;

use Illuminate\Broadcasting\BroadcastManager;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Broadcast;

class BroadcastServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // تعطيل استخدام Pusher في البث ووضع مزود بديل
        config(['broadcasting.default' => 'null']);
        
        Broadcast::routes();

        require base_path('routes/channels.php');
    }
} 