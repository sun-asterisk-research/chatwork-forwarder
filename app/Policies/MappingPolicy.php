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
}
