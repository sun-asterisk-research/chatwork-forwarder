<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Template;
use Illuminate\Auth\Access\HandlesAuthorization;

class TemplatePolicy
{
    use HandlesAuthorization;

    /**
     * Determine  if the given bot can be deleted by user
     *
     * @param App\Models\User
     * @param App\Models\Template
     * @return bool
     */
    public function delete(User $user, Template $template)
    {
        return $user->id === $template->user_id;
    }

    /**
     * Determine  if the given bot can be updated by user
     *
     * @param App\Models\User
     * @param App\Models\Template
     * @return bool
     */
    public function update(User $user, Template $template)
    {
        return $user->id === $template->user_id;
    }

    /**
     * Determine whether the user can view any models.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function viewAny(User $user)
    {
        return true;
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return true;
    }
}
