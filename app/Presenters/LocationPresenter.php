<?php

namespace App\Presenters;

/**
 * Class LocationPresenter
 */
class LocationPresenter extends Presenter
{
    /**
     * Json Column Layout for bootstrap table
     * @return string
     */
    public static function dataTableLayout()
    {
        $layout = [
            [
                'field' => 'bulk_selectable',
                'checkbox' => true,
                'formatter' => 'checkboxEnabledFormatter',
            ],[
                'field' => 'id',
                'searchable' => false,
                'sortable' => true,
                'switchable' => true,
                'title' => trans('general.id'),
                'visible' => false,
            ],[
                'field' => 'name',
                'searchable' => true,
                'sortable' => true,
                'switchable' => false,
                'title' => trans('admin/locations/table.name'),
                'visible' => true,
                'formatter' => 'locationsLinkFormatter',
            ],[
                'field' => 'assets_count',
                'searchable' => false,
                'sortable' => true,
                'switchable' => true,
                'title' =>  trans('admin/locations/message.current_location'),
                'visible' => true,
            ],[
                'field' => 'address',
                'searchable' => true,
                'sortable' => true,
                'switchable' => true,
                'title' =>  trans('admin/locations/table.address'),
                'visible' => true,
            ],[
                'field' => 'address2',
                'searchable' => true,
                'sortable' => true,
                'switchable' => true,
                'title' =>  trans('admin/locations/table.address2'),
                'visible' => false,
            ],[
                'field' => 'city',
                'searchable' => true,
                'sortable' => true,
                'switchable' => true,
                'title' =>  trans('admin/locations/table.city'),
                'visible' => true,
            ],[
                'field' => 'state',
                'searchable' => true,
                'sortable' => true,
                'switchable' => true,
                'title' =>  trans('admin/locations/table.state'),
                'visible' => true,
            ],[
                'field' => 'zip',
                'searchable' => true,
                'sortable' => true,
                'switchable' => true,
                'title' =>  trans('admin/locations/table.zip'),
                'visible' => false,
            ],[
                'field' => 'country',
                'searchable' => true,
                'sortable' => true,
                'switchable' => true,
                'title' =>  trans('admin/locations/table.country'),
                'visible' => false,
            ],[
                'field' => 'phone',
                'searchable' => true,
                'sortable' => true,
                'switchable' => true,
                'title' => trans('admin/users/table.phone'),
                'visible' => false,
                'formatter'    => 'phoneFormatter',
            ],[
                'field' => 'created_at',
                'searchable' => true,
                'sortable' => true,
                'switchable' => true,
                'title' => trans('general.created_at'),
                'visible' => false,
                'formatter' => 'dateDisplayFormatter',
            ],[
                'field' => 'actions',
                'searchable' => false,
                'sortable' => false,
                'switchable' => false,
                'title' => trans('table.actions'),
                'visible' => true,
                'formatter' => 'locationsActionsFormatter',
            ],
        ];

        return json_encode($layout);
    }

    /**
     * Link to this locations name
     * @return string
     */
    public function nameUrl()
    {
        return (string) link_to_route('locations.show', $this->name, $this->id);
    }

    /**
     * Getter for Polymorphism.
     * @return mixed
     */
    public function name()
    {
        return $this->model->name;
    }

    /**
     * Url to view this item.
     * @return string
     */
    public function viewUrl()
    {
        return route('locations.show', $this->id);
    }

    public function glyph()
    {
        return '<i class="fas fa-map-marker-alt" aria-hidden="true"></i>';
    }

    public function fullName()
    {
        return $this->name;
    }
}
