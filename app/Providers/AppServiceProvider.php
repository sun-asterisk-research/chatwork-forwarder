<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Webhook;
use App\Repositories\Interfaces\BaseRepositoryInterface;
use App\Repositories\Eloquents\WebhookRepository;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(BaseRepositoryInterface::class, WebhookRepository::class);
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
