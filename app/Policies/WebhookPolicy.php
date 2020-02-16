<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Webhook;
use App\Enums\UserType;
use Illuminate\Auth\Access\HandlesAuthorization;

class WebhookPolicy
{
    use HandlesAuthorization;

    /**
     * Determine if the user can update the webhook.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Webhook  $webhook
     * @return mixed
     */
    public function update(User $user, Webhook $webhook)
    {
        return $user->id === $webhook->user_id;
    }

    /**
     * Determine if the user can create the webhook.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Webhook  $webhook
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->role === UserType::USER;
    }

    /**
     * Determine if the user can enable/disable the webhook.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Webhook  $webhook
     * @return mixed
     */
    public function changeStatus(User $user, Webhook $webhook)
    {
        return $user->id === $webhook->user_id || $user->role === UserType::ADMIN;
    }

    /**
     * Determine if the user can delete the webhook.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Webhook  $webhook
     * @return mixed
     */
    public function delete(User $user, Webhook $webhook)
    {
        return $user->id === $webhook->user_id;
    }
}
