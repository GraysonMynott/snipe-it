<?php

namespace App\Policies;

use App\Models\User;

abstract class CheckoutablePermissionsPolicy extends SnipePermissionsPolicy
{
    /**
     * Determine whether the user can manage the accessory.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function manage(User $user, $item = null)
    {
        return $user->hasAccess($this->columnName().'.edit');
    }
}
