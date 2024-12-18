<?php

namespace App\Presenters;

/**
 * Class LicensePresenter
 */
class LicensePresenter extends Presenter
{
    /**
     * Json Column Layout for bootstrap table
     * Fields are:
     *  - ID
     *  - Company
     *  - Name
     *  - Product Key
     *  - Expiration Date
     *  - License Name
     *  - Category
     *  - Manufacturer
     *  - License Seats
     *  - License Seats Available
     *  - Min Amount
     *  - Purchase Date
     *  - Termination Date
     *  - Reassignable
     *  - Created At
     *  - Updated At
     *  - Notes
     *  - Check In/Out
     *  - Actions
     * @return string
     */
    public static function dataTableLayout()
    {
        $layout = [
            [
                'field' => 'id',
                'searchable' => false,
                'sortable' => true,
                'switchable' => true,
                'title' => trans('general.id'),
                'visible' => false,
            ], [
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
                'switchable' => false,
                'title' => trans('general.name'),
                'formatter' => 'licensesLinkFormatter',
            ], [
                'field' => 'product_key',
                'searchable' => true,
                'sortable' => true,
                'title' => trans('admin/licenses/form.license_key'),
                'formatter' => 'licensesLinkFormatter',
            ], [
                'field' => 'expiration_date',
                'searchable' => true,
                'sortable' => true,
                'title' => trans('admin/licenses/form.expiration'),
                'formatter' => 'dateDisplayFormatter',
            ], [
//                'field' => 'license_email',
//                'searchable' => true,
//                'sortable' => true,
//                'title' => trans('admin/licenses/form.to_email'),
//            ], [
                'field' => 'license_name',
                'searchable' => true,
                'sortable' => true,
                'title' => trans('admin/licenses/form.to_name'),
            ], [
                'field' => 'category',
                'searchable' => true,
                'sortable' => true,
                'switchable' => true,
                'title' => trans('general.category'),
                'visible' => false,
                'formatter' => 'categoriesLinkObjFormatter',
            ], [
                'field' => 'manufacturer',
                'searchable' => true,
                'sortable' => true,
                'title' => trans('general.manufacturer'),
                'formatter' => 'manufacturersLinkObjFormatter',
            ], [
                'field' => 'seats',
                'searchable' => false,
                'sortable' => true,
                'title' => trans('admin/licenses/general.total'),
            ], [
                'field' => 'free_seats_count',
                'searchable' => false,
                'sortable' => true,
                'title' => trans('admin/licenses/general.remaining'),
            ], [
                'field' => 'min_amt',
                'searchable' => false,
                'sortable' => true,
                'title' => trans('mail.min_QTY'),
                'formatter' => 'minAmtFormatter',
            ], [
                'field' => 'purchase_date',
                'searchable' => true,
                'sortable' => true,
                'visible' => false,
                'title' => trans('general.purchase_date'),
                'formatter' => 'dateDisplayFormatter',
            ], [
                'field' => 'termination_date',
                'searchable' => true,
                'sortable' => true,
                'visible' => false,
                'title' => trans('admin/licenses/form.termination_date'),
                'formatter' => 'dateDisplayFormatter',
            ], [
                'field' => 'reassignable',
                'searchable' => false,
                'sortable' => true,
                'visible' => false,
                'title' => trans('admin/licenses/form.reassignable'),
                'formatter' => 'trueFalseFormatter',
            ], [
                'field' => 'created_at',
                'searchable' => false,
                'sortable' => true,
                'visible' => false,
                'title' => trans('general.created_at'),
                'formatter' => 'dateDisplayFormatter',
            ], [
                'field' => 'updated_at',
                'searchable' => false,
                'sortable' => true,
                'visible' => false,
                'title' => trans('general.updated_at'),
                'formatter' => 'dateDisplayFormatter',
            ], [
                'field' => 'notes',
                'searchable' => true,
                'sortable' => true,
                'visible' => false,
                'title' => trans('general.notes'),
                'formatter' => 'notesFormatter',
            ],
        ];

        $layout[] = [
            'field' => 'checkincheckout',
            'searchable' => false,
            'sortable' => false,
            'switchable' => false,
            'title' => trans('general.checkin').'/'.trans('general.checkout'),
            'visible' => true,
            'formatter' => 'licensesInOutFormatter',
        ];

        $layout[] = [
            'field' => 'actions',
            'searchable' => false,
            'sortable' => false,
            'switchable' => false,
            'title' => trans('table.actions'),
            'formatter' => 'licensesActionsFormatter',
        ];

        return json_encode($layout);
    }

    /**
     * Json Column Layout for bootstrap table
     * Fields are:
     *  - ID
     *  - Name
     *  - Assigned Asset
     *  - Location
     *  - Notes
     *  - Check In/Out
     *  - Actions
     * @return string
     */
    public static function dataTableLayoutSeats()
    {
        $layout = [
            [
                'field' => 'id',
                'searchable' => false,
                'sortable' => true,
                'switchable' => true,
                'title' => trans('general.id'),
                'visible' => false,
           ], [
                'field' => 'name',
                'searchable' => false,
                'sortable' => false,
                'sorter'   => 'numericOnly',
                'switchable' => true,
                'title' => trans('admin/licenses/general.seat'),
                'visible' => true,
            ], [
//                'field' => 'assigned_user',
//                'searchable' => false,
//                'sortable' => false,
//                'switchable' => true,
//                'title' => trans('admin/licenses/general.user'),
//                'visible' => true,
//                'formatter' => 'usersLinkObjFormatter',
//            ], [
//                'field' => 'assigned_user.email',
//                'searchable' => false,
//                'sortable' => false,
//                'switchable' => true,
//                'title' => trans('admin/users/table.email'),
//                'visible' => true,
//                'formatter' => 'emailFormatter',
//            ], [
                'field' => 'assigned_asset',
                'searchable' => false,
                'sortable' => false,
                'switchable' => true,
                'title' => trans('admin/licenses/form.asset'),
                'visible' => true,
                'formatter' => 'hardwareLinkObjFormatter',
            ], [
                'field' => 'location',
                'searchable' => false,
                'sortable' => false,
                'switchable' => true,
                'title' => trans('general.location'),
                'visible' => true,
                'formatter' => 'locationsLinkObjFormatter',
            ], [
                'field' => 'notes',
                'searchable' => false,
                'sortable' => false,
                'visible' => false,
                'title' => trans('general.notes'),
                'formatter' => 'notesFormatter'
            ], [
                'field' => 'checkincheckout',
                'searchable' => false,
                'sortable' => false,
                'switchable' => false,
                'title' => trans('general.checkin').'/'.trans('general.checkout'),
                'visible' => true,
                'formatter' => 'licenseSeatInOutFormatter',
            ],
        ];

        return json_encode($layout);
    }

    /**
     * Link to this licenses Name
     * @return string
     */
    public function nameUrl()
    {
        return (string) link_to_route('licenses.show', $this->name, $this->id);
    }

    /**
     * Link to this licenses Name
     * @return string
     */
    public function fullName()
    {
        return $this->name;
    }

    /**
     * Link to this licenses serial
     * @return string
     */
    public function serialUrl()
    {
        return (string) link_to('/licenses/'.$this->id, mb_strimwidth($this->serial, 0, 50, '...'));
    }

    /**
     * Url to view this item.
     * @return string
     */
    public function viewUrl()
    {
        return route('licenses.show', $this->id);
    }
}
