<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Webhook;
use App\Repositories\Interfaces\BaseRepositoryInterface;
use App\Repositories\Eloquents\WebhookRepository;

class AppServiceProvider extends ServiceProvider
{
    protected $repositories = [
        'WebhookRepository'
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
     * register repositories class dependency.
     * example change drive to call method only change.
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
        //
    }
}
