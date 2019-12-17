<?php

namespace App\Policies;

use App\Models\Mapping;
use App\Models\User;
use App\Models\Webhook;
use Illuminate\Auth\Access\HandlesAuthorization;

class MappingPolicy
{
    use HandlesAuthorization;

    /**
     * Determine  if the given mapping can be deleted by user
     *
     * @param App\Models\User
     * @param App\Models\Webhook
     * @param App\Models\Mapping
     * @return bool
     */
    public function delete(User $user, Mapping $mapping, Webhook $webhook)
    {
        return $webhook->id === $mapping->webhook->id && $user->id === $webhook->user_id;
    }

    /**
     * Determine if the given mapping can be create by user and webhook
     *
     * @param App\Models\User
     * @param App\Models\Webhook
     * @return bool
     */
    public function create(User $user, Mapping $mapping, Webhook $webhook)
    {
        return $user->id === $webhook->user_id;
    }
}
