<?php

namespace App\Presenters;

use App\Helpers\Helper;
use App\Models\Setting;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Class UserPresenter
 */
class UserPresenter extends Presenter
{
    /**
     * Json Column Layout for bootstrap table
     * @return string
     */
    public static function dataTableLayout()
    {
        $layout = [
            [
                'field' => 'checkbox',
                'checkbox' => true,
            ],
            // [
            //     'field' => 'id',
            //     'searchable' => false,
            //     'sortable' => true,
            //     'switchable' => true,
            //     'title' => trans('general.id'),
            //     'visible' => false,
            // ],
            // [
            //     'field' => 'avatar',
            //     'searchable' => false,
            //     'sortable' => false,
            //     'switchable' => true,
            //     'title' => trans('general.importer.avatar'),
            //     'visible' => false,
            //     'formatter' => 'imageFormatter',
            // ],
            [
                'field' => 'company',
                'searchable' => true,
                'sortable' => true,
                'switchable' => true,
                'title' => trans('admin/companies/table.title'),
                'visible' => false,
                'formatter' => 'companiesLinkObjFormatter',
            ],
            [
                'field' => 'name',
                'searchable' => true,
                'sortable' => true,
                'title' => trans('admin/users/table.name'),
                'visible' => true,
                'formatter' => 'usersLinkFormatter',
            ],

            [
                'field' => 'first_name',
                'searchable' => true,
                'sortable' => true,
                'title' => trans('general.first_name'),
                'visible' => false,
                'formatter' => 'usersLinkFormatter',
            ],

            [
                'field' => 'last_name',
                'searchable' => true,
                'sortable' => true,
                'title' => trans('general.last_name'),
                'visible' => false,
                'formatter' => 'usersLinkFormatter',
            ],
            // [
            //     'field' => 'jobtitle',
            //     'searchable' => true,
            //     'sortable' => true,
            //     'switchable' => true,
            //     'title' => trans('admin/users/table.title'),
            //     'visible' => true,
            //     'formatter' => 'usersLinkFormatter',
            // ],
            // [
            //     'field' => 'vip',
            //     'searchable' => false,
            //     'sortable' => true,
            //     'switchable' => true,
            //     'title' => trans('admin/users/general.vip_label'),
            //     'visible' => false,
            //     'formatter' => 'trueFalseFormatter',
            // ],
            // [
            //     'field' => 'remote',
            //     'searchable' => false,
            //     'sortable' => true,
            //     'switchable' => true,
            //     'title' => trans('admin/users/general.remote'),
            //     'visible' => false,
            //     'formatter' => 'trueFalseFormatter',
            // ],
            [
                'field' => 'email',
                'searchable' => true,
                'sortable' => true,
                'switchable' => true,
                'title' => trans('admin/users/table.email'),
                'visible' => true,
                'formatter' => 'emailFormatter',
            ],
            // [
            //     'field' => 'phone',
            //     'searchable' => true,
            //     'sortable' => true,
            //     'switchable' => true,
            //     'title' => trans('admin/users/table.phone'),
            //     'visible' => true,
            //     'formatter'    => 'phoneFormatter',
            // ],
            [
                'field' => 'username',
                'searchable' => true,
                'sortable' => true,
                'switchable' => false,
                'title' => trans('admin/users/table.username'),
                'visible' => true,
                'formatter' => 'usersLinkFormatter',
            ],
            [
                'field' => 'notes',
                'searchable' => true,
                'sortable' => true,
                'switchable' => true,
                'title' => trans('general.notes'),
                'visible' => true,
            ],
            [
                'field' => 'groups',
                'searchable' => false,
                'sortable' => false,
                'switchable' => true,
                'title' => trans('general.groups'),
                'visible' => true,
                'formatter' => 'groupsFormatter',
            ],
            // [
            //     'field' => 'ldap_import',
            //     'searchable' => false,
            //     'sortable' => true,
            //     'switchable' => true,
            //     'title' => trans('admin/settings/general.ldap_enabled'),
            //     'visible' => false,
            //     'formatter' => 'trueFalseFormatter',
            // ],
            // [
            //     'field' => 'two_factor_enrolled',
            //     'searchable' => false,
            //     'sortable' => true,
            //     'switchable' => true,
            //     'title' => trans('admin/users/general.two_factor_enrolled'),
            //     'visible' => false,
            //     'formatter' => 'trueFalseFormatter',
            // ],
            // [
            //     'field' => 'two_factor_optin',
            //     'searchable' => false,
            //     'sortable' => true,
            //     'switchable' => true,
            //     'title' => trans('admin/users/general.two_factor_active'),
            //     'visible' => false,
            //     'formatter' => 'trueFalseFormatter',
            // ],
            [
                'field' => 'activated',
                'searchable' => false,
                'sortable' => true,
                'switchable' => true,
                'title' => trans('general.login_enabled'),
                'visible' => true,
                'formatter' => 'trueFalseFormatter',
            ],
            // [
            //     'field' => 'autoassign_licenses',
            //     'searchable' => false,
            //     'sortable' => true,
            //     'switchable' => true,
            //     'title' => trans('general.autoassign_licenses'),
            //     'visible' => false,
            //     'formatter' => 'trueFalseFormatter',
            // ],
            // [
            //     'field' => 'created_by',
            //     'searchable' => false,
            //     'sortable' => true,
            //     'title' => trans('general.created_by'),
            //     'visible' => false,
            //     'formatter' => 'usersLinkObjFormatter',
            // ],
            [
                'field' => 'created_at',
                'searchable' => true,
                'sortable' => true,
                'switchable' => true,
                'title' => trans('general.created_at'),
                'visible' => false,
                'formatter' => 'dateDisplayFormatter',
            ],
            // [
            //     'field' => 'start_date',
            //     'searchable' => true,
            //     'sortable' => true,
            //     'switchable' => true,
            //     'title' => trans('general.start_date'),
            //     'visible' => false,
            //     'formatter' => 'dateDisplayFormatter',
            // ],
            // [
            //     'field' => 'end_date',
            //     'searchable' => true,
            //     'sortable' => true,
            //     'switchable' => true,
            //     'title' => trans('general.end_date'),
            //     'visible' => false,
            //     'formatter' => 'dateDisplayFormatter',
            // ],
            [
                'field' => 'last_login',
                'searchable' => false,
                'sortable' => true,
                'switchable' => true,
                'title' => trans('general.last_login'),
                'visible' => false,
                'formatter' => 'dateDisplayFormatter',
            ],
            [
                'field' => 'actions',
                'searchable' => false,
                'sortable' => false,
                'switchable' => false,
                'title' => trans('table.actions'),
                'visible' => true,
                'formatter' => 'usersActionsFormatter',
            ],
        ];

        return json_encode($layout);
    }

    public function emailLink()
    {
        if ($this->email) {
            return '<a href="mailto:'.$this->email.'">'.$this->email.'</a><a href="mailto:'.$this->email.'" class="hidden-xs hidden-sm"><i class="far fa-envelope"></i></a>';
        }

        return '';
    }

    /**
     * Returns the user full name, it simply concatenates
     * the user first and last name.
     *
     * @return string
     */
    public function fullName()
    {
        return html_entity_decode($this->first_name.' '.$this->last_name, ENT_QUOTES | ENT_XML1, 'UTF-8');
    }

    /**
     * Standard accessor.
     * @TODO Remove presenter::fullName() entirely?
     * @return string
     */
    public function name()
    {
        return $this->fullName();
    }

    /**
     * Returns the user Gravatar image url.
     *
     * @return string
     */
    public function gravatar()
    {

        // User's specific avatar
        if ($this->avatar) {

            // Check if it's a google avatar or some external avatar
            if (Str::startsWith($this->avatar, ['http://', 'https://'])) {
                return $this->avatar;
            }

            // Otherwise assume it's an uploaded image
            return Storage::disk('public')->url('avatars/'.e($this->avatar));
        }

        // If there is a default avatar
        if (Setting::getSettings()->default_avatar!= '') {
            return Storage::disk('public')->url('avatars/'.e(Setting::getSettings()->default_avatar));
        }

        // Fall back to Gravatar if the settings allow loading remote scripts
        if (Setting::getSettings()->load_remote == '1') {
            if ($this->model->gravatar != '') {

                $gravatar = md5(strtolower(trim($this->model->gravatar)));
                return '//gravatar.com/avatar/'.$gravatar;

            } elseif ($this->email != '') {

                $gravatar = md5(strtolower(trim($this->email)));
                return '//gravatar.com/avatar/'.$gravatar;
            }
        }


        return false;
    }

    /**
     * Formatted url for use in tables.
     * @return string
     */
    public function nameUrl()
    {
        return (string) link_to_route('users.show', $this->getFullNameAttribute(), $this->id);
    }

    /**
     * Url to view this item.
     * @return string
     */
    public function viewUrl()
    {
        return route('users.show', $this->id);
    }

    public function glyph()
    {
        return '<i class="fas fa-user" aria-hidden="true"></i>';
    }
}
