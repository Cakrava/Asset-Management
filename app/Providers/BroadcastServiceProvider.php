<?php

namespace App\Providers;

use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\ServiceProvider;

class BroadcastServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Mendaftarkan route /broadcasting/auth
        Broadcast::routes();

        // Memuat definisi channel dari routes/channels.php
        require base_path('routes/channels.php');
    }
}