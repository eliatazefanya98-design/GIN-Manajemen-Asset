<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

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
        \Illuminate\Support\Facades\View::composer('components.navbar', function ($view) {
            $notificationData = ['unread_count' => 0, 'items' => []];

            if (\Illuminate\Support\Facades\Auth::check()) {
                $notificationData = app(\App\Services\NotificationService::class)->getNotifications(\Illuminate\Support\Facades\Auth::user());
            }

            $view->with('systemNotifications', $notificationData);
        });
    }
}
