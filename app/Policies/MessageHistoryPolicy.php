<?php

namespace App\Policies;

use App\Models\User;
use App\Enums\UserType;
use App\Models\MessageHistory;
use Illuminate\Auth\Access\HandlesAuthorization;

class MessageHistoryPolicy
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
        if ($user->role === UserType::ADMIN) {
            return true;
        }
    }

    /**
     * Determine if the given message history can be deleted by user
     *
     * @param App\Models\User
     * @param App\Models\MessageHistory
     * @return bool
     */
    public function delete(User $user, MessageHistory $messageHistory)
    {
        return $user->id === $messageHistory->payloadHistory->webhook->user_id;
    }
}
