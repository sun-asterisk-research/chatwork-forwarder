<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Template;
use App\Enums\TemplateStatus;
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
        return $user->id === $template->user_id && $template->status !== TemplateStatus::STATUS_PUBLIC;
    }
}
