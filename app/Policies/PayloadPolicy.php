<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Payload;
use App\Models\Webhook;
use App\Enums\UserType;
use Illuminate\Auth\Access\HandlesAuthorization;

class PayloadPolicy
{
    use HandlesAuthorization;

    /**
     * Determine  if the given payload can be deleted by user
     *
     * @param App\Models\User
     * @param App\Models\Payload
     * @param App\Models\Webhook
     * @return bool
     */
    public function delete(User $user, Payload $payload, Webhook $webhook)
    {
        return $webhook->id === $payload->webhook_id && $user->id === $webhook->user_id;
    }

    /**
     * Determine if the given payload can be updated by user
     *
     * @param App\Models\User
     * @param App\Models\Payload
     * @param App\Models\Webhook
     * @return bool
     */
    public function update(User $user, Payload $payload, Webhook $webhook)
    {
        return $webhook->id === $payload->webhook_id && $user->id === $webhook->user_id;
    }

    /**
     * Perform pre-authorization checks.
     *
     * @param  \App\Models\User  $user
     * @param  string  $ability
     * @return void|bool
     */
    public function before(User $user, $bot)
    {
        if ($user->role == UserType::ADMIN) {
            return true;
        }
    }
}
