<?php

namespace App\Http\Controllers;

use App\Models\Actionlog;
use App\Models\Asset;
use App\Models\AssetModel;
use App\Models\Company;
use App\Models\Setting;
use App\Models\User;
use App\Notifications\RequestAssetCancelation;
use App\Notifications\RequestAssetNotification;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use \Illuminate\Contracts\View\View;

/**
 * This controller handles all actions related to the ability for users
 * to view their own assets in the Snipe-IT Asset Management application.
 *
 * @version    v1.0
 */
class ViewAssetsController extends Controller
{
    /**
     * Redirect to the profile page.
     *
     */
    public function getIndex() : View | RedirectResponse
    {
        $user = User::with(
            'assets',
            'assets.model',
            'assets.model.fieldset.fields',
            'licenses',
        )->find(auth()->id());

        $field_array = array();

        // Loop through all the custom fields that are applied to any model the user has assigned
        foreach ($user->assets as $asset) {

            // Make sure the model has a custom fieldset before trying to loop through the associated fields
            if ($asset->model->fieldset) {

                foreach ($asset->model->fieldset->fields as $field) {
                    // check and make sure they're allowed to see the value of the custom field
                    if ($field->display_in_user_view == '1') {
                        $field_array[$field->db_column] = $field->name;
                    }
                    
                }
            }

        }

        // Since some models may re-use the same fieldsets/fields, let's make the array unique so we don't repeat columns
        array_unique($field_array);

        if (isset($user->id)) {
            return view('account/view-assets', compact('user', 'field_array' ))
                ->with('settings', Setting::getSettings());
        }

        // Redirect to the user management page
        return redirect()->route('users.index')
            ->with('error', trans('admin/users/message.user_not_found', $user->id));
    }
}
