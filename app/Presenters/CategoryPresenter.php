<?php

namespace App\Presenters;

/**
 * Class CategoryPresenter
 */
class CategoryPresenter extends Presenter
{
    /**
     * Json Column Layout for bootstrap table
     * Fields are:
     *  - ID
     *  - Name
     *  - Image
     *  - Category Type
     *  - Item Count
     *  - Checkin Email
     *  - Created At
     *  - Updated At
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
                'field' => 'name',
                'searchable' => true,
                'sortable' => true,
                'switchable' => false,
                'title' => trans('general.name'),
                'visible' => true,
                'formatter' => 'categoriesLinkFormatter',
            ], [
                'field' => 'image',
                'searchable' => false,
                'sortable' => true,
                'title' => trans('general.image'),
                'visible' => true,
                'formatter' => 'imageFormatter',
            ], [
                'field' => 'category_type',
                'searchable' => true,
                'sortable' => true,
                'title' => trans('general.type'),
                'visible' => true,
            ], [
                'field' => 'item_count',
                'searchable' => false,
                'sortable' => true,
                'title' => trans('general.qty'),
                'visible' => true,
            ], [
                'field' => 'checkin_email',
                'searchable' => false,
                'sortable' => true,
                'class' => 'css-envelope',
                'title' => 'Send Email',
                'visible' => true,
                'formatter' => 'trueFalseFormatter',
            ], [
                'field' => 'created_at',
                'searchable' => true,
                'sortable' => true,
                'visible' => false,
                'title' => trans('general.created_at'),
                'formatter' => 'dateDisplayFormatter',
            ], [
                'field' => 'updated_at',
                'searchable' => true,
                'sortable' => true,
                'visible' => false,
                'title' => trans('general.updated_at'),
                'formatter' => 'dateDisplayFormatter',
            ], [
                'field' => 'actions',
                'searchable' => false,
                'sortable' => false,
                'switchable' => false,
                'title' => trans('table.actions'),
		        'formatter' => 'categoriesActionsFormatter',
            ],
        ];

        return json_encode($layout);
    }

    /**
     * Link to this categories name
     * @return string
     */
    public function nameUrl()
    {
        return (string) link_to_route('categories.show', $this->name, $this->id);
    }

    /**
     * Url to view this item.
     * @return string
     */
    public function viewUrl()
    {
        return route('categories.show', $this->id);
    }
}
