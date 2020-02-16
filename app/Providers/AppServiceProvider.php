<?php

namespace App\Providers;

use Auth;
use App\Enums\UserType;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    protected $repositories = [
        'WebhookRepository',
        'BotRepository',
        'PayloadRepository',
        'PayloadHistoryRepository',
        'UserRepository',
        'MessageHistoryRepository',
        'MappingRepository',
        'ChatworkRepository',
    ];

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->registerRepositories();
    }

    /**
     * Register repositories class dependency.
     * Example change drive to call method only change.
     */
    private function registerRepositories()
    {
        foreach ($this->repositories as $repository) {
            $this->app->bindIf(
                'App\\Repositories\\Interfaces\\' . $repository . 'Interface',
                'App\\Repositories\\Eloquents\\'. $repository
            );
        }
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Blade::if('admin', function () {
            return Auth::user() && Auth::user()->role == UserType::ADMIN;
        });
    }
}
