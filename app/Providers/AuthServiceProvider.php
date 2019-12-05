<?php

namespace App\Providers;

use App\Models\Bot;
use App\Models\Webhook;
use App\Models\Payload;
use App\Policies\BotPolicy;
use App\Policies\WebhookPolicy;
use App\Policies\PayloadPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        Bot::class => BotPolicy::class,
        Webhook::class => WebhookPolicy::class,
        Payload::class => PayloadPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();
    }
}
