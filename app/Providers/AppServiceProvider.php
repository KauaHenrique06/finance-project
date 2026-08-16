<?php

namespace App\Providers;

use App\Models\Notification;
use App\Models\WhatsappInstance;
use App\Observers\NotificationObserver;
use App\Observers\WhatsappInstanceObserver;
use Dedoc\Scramble\Scramble;
use Illuminate\Routing\Route;
use Illuminate\Support\ServiceProvider;
use Str;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Notification::observe(NotificationObserver::class);
        WhatsappInstance::observe(WhatsappInstanceObserver::class);
        Scramble::configure()
            ->routes(function (Route $route) {
                return Str::startsWith($route->uri, 'api/');
            });
    }
}
