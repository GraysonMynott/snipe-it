<?php

namespace App\Policies;

use App\Models\User;

class AssetPolicy extends CheckoutablePermissionsPolicy
{
    protected function columnName()
    {
        return 'assets';
    }

    public function viewRequestable(User $user, Asset $asset = null)
    {
        return $user->hasAccess('assets.view.requestable');
    }

    public function patch(User $user, Asset $asset = null)
    {
        return $user->hasAccess('assets.patch');
    }
}
