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
     * Fields are:
     *  - Checkbox
     *  - Company
     *  - Name
     *  - First Name
     *  - Last Name
     *  - Email
     *  - Username
     *  - Notes
     *  - Groups
     *  - Activated
     *  - Created Date
     *  - Last Login
     *  - Actions
     * @return string
     */
    public static function dataTableLayout()
    {
        $layout = [
            [
                'field' => 'checkbox',
                'checkbox' => true,
            ], [
            //     'field' => 'id',
            //     'searchable' => false,
            //     'sortable' => true,
            //     'switchable' => true,
            //     'title' => trans('general.id'),
            //     'visible' => false,
            // ], [
                'field' => 'company',
                'searchable' => true,
                'sortable' => true,
                'switchable' => true,
                'title' => trans('admin/companies/table.title'),
                'visible' => false,
                'formatter' => 'companiesLinkObjFormatter',
            ], [
                'field' => 'name',
                'searchable' => true,
                'sortable' => true,
                'title' => trans('admin/users/table.name'),
                'visible' => true,
                'formatter' => 'usersLinkFormatter',
            ], [
                'field' => 'first_name',
                'searchable' => true,
                'sortable' => true,
                'title' => trans('general.first_name'),
                'visible' => false,
                'formatter' => 'usersLinkFormatter',
            ], [
                'field' => 'last_name',
                'searchable' => true,
                'sortable' => true,
                'title' => trans('general.last_name'),
                'visible' => false,
                'formatter' => 'usersLinkFormatter',
            ], [
                'field' => 'email',
                'searchable' => true,
                'sortable' => true,
                'switchable' => true,
                'title' => trans('admin/users/table.email'),
                'visible' => true,
                'formatter' => 'emailFormatter',
            ], [
                'field' => 'username',
                'searchable' => true,
                'sortable' => true,
                'switchable' => false,
                'title' => trans('admin/users/table.username'),
                'visible' => true,
                'formatter' => 'usersLinkFormatter',
            ], [
                'field' => 'notes',
                'searchable' => true,
                'sortable' => true,
                'switchable' => true,
                'title' => trans('general.notes'),
                'visible' => true,
            ], [
                'field' => 'groups',
                'searchable' => false,
                'sortable' => false,
                'switchable' => true,
                'title' => trans('general.groups'),
                'visible' => true,
                'formatter' => 'groupsFormatter',
            ], [
                'field' => 'activated',
                'searchable' => false,
                'sortable' => true,
                'switchable' => true,
                'title' => trans('general.login_enabled'),
                'visible' => true,
                'formatter' => 'trueFalseFormatter',
            ], [
            //     'field' => 'created_by',
            //     'searchable' => false,
            //     'sortable' => true,
            //     'title' => trans('general.created_by'),
            //     'visible' => false,
            //     'formatter' => 'usersLinkObjFormatter',
            // ], [
                'field' => 'created_at',
                'searchable' => true,
                'sortable' => true,
                'switchable' => true,
                'title' => trans('general.created_at'),
                'visible' => false,
                'formatter' => 'dateDisplayFormatter',
            ], [
                'field' => 'last_login',
                'searchable' => false,
                'sortable' => true,
                'switchable' => true,
                'title' => trans('general.last_login'),
                'visible' => false,
                'formatter' => 'dateDisplayFormatter',
            ], [
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
     * @TODO [GM] Remove presenter::name() entirely?
     * @return string
     */
//    public function name()
//    {
//        return $this->fullName();
//    }

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

    /**
     * @TODO [GM] Remove presenter::glyph() entirely?
     * @return string
     */
//    public function glyph()
//    {
//        return '<i class="fas fa-user" aria-hidden="true"></i>';
//    }
}
