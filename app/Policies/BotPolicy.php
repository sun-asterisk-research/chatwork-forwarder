<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Bot;
use App\Enums\UserType;
use Illuminate\Auth\Access\HandlesAuthorization;

class BotPolicy
{
    use HandlesAuthorization;

    /**
     * Determine if the current user is admin can do anything
     *
     * @param App\Models\User
     * @return bool
     */
    public function before($user, $ability)
    {
        if ($user->role == UserType::ADMIN) {
            return false;
        }
    }

    /**
     * Determine if the given bot can be deleted by user
     *
     * @param App\Models\User
     * @param App\Models\Bot
     * @return bool
     */
    public function delete(User $user, Bot $bot)
    {
        return $user->id === $bot->user_id;
    }

    /**
     * Determine if the given bot can be updated by user
     *
     * @param App\Models\User
     * @param App\Models\Bot
     * @return bool
     */
    public function update(User $user, Bot $bot)
    {
        return $user->id === $bot->user_id;
    }

    /**
     * Determine if the user can view any models.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function viewAny(User $user)
    {
        return true;
    }

    /**
     * Determine if the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return true;
    }

    /**
     * Determine if the user can get room of bot.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function getRoom(User $user, $bot)
    {
        return $user->id === $bot->user_id;
    }
}
