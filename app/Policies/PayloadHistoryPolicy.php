<?php

namespace App\Policies;

use App\Models\User;
use App\Enums\UserType;
use App\Models\PayloadHistory;
use Illuminate\Auth\Access\HandlesAuthorization;

class PayloadHistoryPolicy
{
    use HandlesAuthorization;

    /**
     * Determine  if the current user is admin can do anything
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
     * Determine  if the given payload history can be showed by user
     *
     * @param App\Models\User
     * @param App\Models\PayloadHistory
     * @return bool
     */
    public function show(User $user, PayloadHistory $payloadHistory)
    {
        return $user->id === $payloadHistory->webhook->user_id;
    }

    /**
     * Determine  if the given payload history can be showed by user
     *
     * @param App\Models\User
     * @param App\Models\PayloadHistory
     * @return bool
     */
    public function delete(User $user, PayloadHistory $payloadHistory)
    {
        return $user->id === $payloadHistory->webhook->user_id;
    }

    public function recheck(User $user, PayloadHistory $payloadHistory)
    {
        return $user->id === $payloadHistory->webhook->user_id;
    }
}
