<?php

namespace App\Providers;

use App\Models\Bot;
use App\Models\Payload;
use App\Models\Webhook;
use App\Policies\BotPolicy;
use App\Models\PayloadHistory;
use App\Policies\PayloadPolicy;
use App\Policies\WebhookPolicy;
use Illuminate\Support\Facades\Gate;
use App\Policies\PayloadHistoryPolicy;
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
        PayloadHistory::class => PayloadHistoryPolicy::class,
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
