<?php

namespace App\Presenters;

use App\Models\CustomField;
use Carbon\CarbonImmutable;
use DateTime;

/**
 * Class AssetPresenter
 */
class AssetPresenter extends Presenter
{
    /**
     * Json Column Layout for bootstrap table
     * Fields are:
     *  - Checkbox
     *  - ID
     *  - Company
     *  - Name
     *  - Image
     *  - Serial
     *  - MAC Address
     *  - Manufacturer
     *  - Model
     *  - Model Number
     *  - Category
     *  - Status
     *  - Location
     *  - RTD Location
     *  - EoL
     *  - EoS
     *  - Notes
     *  - Created At
     *  - Updated At
     *  - Last Patch Date
     *  - Next Patch Date
     * @return string
     */
    public static function dataTableLayout()
    {
        $layout = [
            [
                'field' => 'checkbox',
                'checkbox' => true,
            ], [
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
                'title' => trans('general.company'),
                'visible' => true,
                'formatter' => 'assetCompanyObjFilterFormatter',
            ], [
                'field' => 'name',
                'searchable' => true,
                'sortable' => true,
                'title' => trans('admin/assets/form.name'),
                'visible' => true,
                'formatter' => 'hardwareLinkFormatter',
            ], [
                'field' => 'image',
                'searchable' => false,
                'sortable' => true,
                'switchable' => true,
                'title' => trans('admin/assets/table.image'),
                'visible' => true,
                'formatter' => 'imageFormatter',
            ], [
            //     'field' => 'asset_tag',
            //     'searchable' => true,
            //     'sortable' => true,
            //     'switchable' => false,
            //     'title' => trans('admin/assets/table.asset_tag'),
            //     'visible' => true,
            //     'formatter' => 'hardwareLinkFormatter',
            // ], [
                'field' => 'serial',
                'searchable' => true,
                'sortable' => true,
                'title' => trans('admin/assets/form.serial'),
                'visible' => true,
                'formatter' => 'hardwareLinkFormatter',
            ],  [
                'field' => 'mac_address',
                'searchable' => true,
                'sortable' => true,
                'title' => trans('admin/assets/form.mac_address'),
                'visible' => true,
                'formatter' => 'hardwareLinkFormatter',
            ],  [
                'field' => 'manufacturer',
                'searchable' => true,
                'sortable' => true,
                'title' => trans('general.manufacturer'),
                'visible' => true,
                'formatter' => 'manufacturersLinkObjFormatter',
            ], [
                'field' => 'model',
                'searchable' => true,
                'sortable' => true,
                'title' => trans('admin/assets/form.model'),
                'visible' => true,
                'formatter' => 'modelsLinkObjFormatter',
            ], [
                'field' => 'model_number',
                'searchable' => true,
                'sortable' => true,
                'title' => trans('admin/models/table.modelnumber'),
                'visible' => false,
            ], [
                'field' => 'category',
                'searchable' => true,
                'sortable' => true,
                'title' => trans('general.category'),
                'visible' => true,
                'formatter' => 'categoriesLinkObjFormatter',
            ], [
                'field' => 'status_label',
                'searchable' => true,
                'sortable' => true,
                'title' => trans('admin/assets/table.status'),
                'visible' => true,
                'formatter' => 'statuslabelsLinkObjFormatter',
            ], [
                'field' => 'location',
                'searchable' => true,
                'sortable' => true,
                'title' => trans('admin/assets/table.location'),
                'visible' => true,
                'formatter' => 'deployedLocationFormatter',
            ], [
                'field' => 'rtd_location',
                'searchable' => true,
                'sortable' => true,
                'title' => trans('admin/assets/form.default_location'),
                'visible' => false,
                'formatter' => 'deployedLocationFormatter',
            ], [
                'field' => 'eol',
                'searchable' => false,
                'sortable' => true,
                'visible' => false,
                'title' => trans('admin/assets/form.eol_date'),
            ], [
                'field' => 'eos',
                'searchable' => false,
                'sortable' => true,
                'visible' => false,
                'title' => trans('admin/assets/form.eos_date'),
            ], [
                'field' => 'notes',
                'searchable' => true,
                'sortable' => true,
                'visible' => false,
                'title' => trans('general.notes'),
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
                'field' => 'last_patch_date',
                'searchable' => false,
                'sortable' => true,
                'visible' => false,
                'title' => trans('general.last_patch'),
                'formatter' => 'dateDisplayFormatter',
            ], [
                'field' => 'next_patch_date',
                'searchable' => false,
                'sortable' => true,
                'visible' => false,
                'title' => trans('general.next_patch_date'),
                'formatter' => 'dateDisplayFormatter',
            ],
        ];

        // This looks complicated, but we have to confirm that the custom fields exist in custom fieldsets
        // *and* those fieldsets are associated with models, otherwise we'll trigger
        // javascript errors on the bootstrap tables side of things, since we're asking for properties
        // on fields that will never be passed through the REST API since they're not associated with
        // models. We only pass the fieldsets that pertain to each asset (via their model) so that we
        // don't junk up the REST API with tons of custom fields that don't apply

        $fields = CustomField::whereHas('fieldset', function ($query) {
            $query->whereHas('models');
        })->get();

        // Note: We do not need to e() escape the field names here, as they are already escaped when
        // they are presented in the blade view. If we escape them here, custom fields with quotes in their
        // name can break the listings page. - snipe
        foreach ($fields as $field) {
            $layout[] = [
                'field' => 'custom_fields.'.$field->db_column,
                'searchable' => true,
                'sortable' => true,
                'switchable' => true,
                'title' => $field->name,
                'formatter'=> 'customFieldsFormatter',
                'escape' => true,
                'class' => ($field->field_encrypted == '1') ? 'css-padlock' : '',
                'visible' => ($field->show_in_listview == '1') ? true : false,
            ];
        }

        $layout[] = [
            'field' => 'actions',
            'searchable' => false,
            'sortable' => false,
            'switchable' => false,
            'title' => trans('table.actions'),
            'formatter' => 'hardwareActionsFormatter',
        ];

        return json_encode($layout);
    }

    /**
     * Generate html link to this items name.
     * @return string
     */
    public function nameUrl()
    {
        return (string) link_to_route('hardware.show', e($this->name), $this->id);
    }

    public function modelUrl()
    {
        if ($this->model->model) {
            return $this->model->model->present()->nameUrl();
        }

        return '';
    }

    public function modelEol()
    {
        if ($this->model->model) {
            return $this->model->model->present()->nameUrl();
        }

        return '';
    }

    /**
     * Generate img tag to this items image.
     * @return mixed|string
     */
    public function imageUrl()
    {
        $imagePath = '';
        if ($this->image && ! empty($this->image)) {
            $imagePath = $this->image;
            $imageAlt = $this->name;
        } elseif ($this->model && ! empty($this->model->image)) {
            $imagePath = $this->model->image;
            $imageAlt = $this->model->name;
        }
        $url = config('app.url');
        if (! empty($imagePath)) {
            $imagePath = '<img src="'.$url.'/uploads/assets/'.$imagePath.' height="50" width="50" alt="'.$imageAlt.'">';
        }

        return $imagePath;
    }

    /**
     * Generate img tag to this items image.
     * @return mixed|string
     */
    public function imageSrc()
    {
        $imagePath = '';
        if ($this->image && ! empty($this->image)) {
            $imagePath = $this->image;
        } elseif ($this->model && ! empty($this->model->image)) {
            $imagePath = $this->model->image;
        }
        if (! empty($imagePath)) {
            return config('app.url').'/uploads/assets/'.$imagePath;
        }

        return $imagePath;
    }

    /**
     * Get Displayable Name
     * @return string
     *
     * @todo this should be factored out - it should be subsumed by fullName (below)
     *
     **/
    public function name()
    {
        return $this->fullName;
    }

    /**
     * Helper for notification polymorphism.
     * @return mixed
     */
    public function fullName()
    {
        $str = '';

        // Asset name
        if ($this->model->name) {
            $str .= $this->model->name;
        }

        // Asset tag
        if ($this->asset_tag) {
            $str .= ' ('.$this->model->asset_tag.')';
        }

        // Asset Model name
        if ($this->model->model) {
            $str .= ' - '.$this->model->model->name;
        }

        return $str;
    }

    /**
     * Returns the date this item hits EoL.
     * @return false|string
     */
    public function eol_date()
    {
        if (($this->model->model) && ($this->model->model->eol)) {
//            return $this->model->model->eol->format('Y-m-d');
            return $this->model->model->eolText;
        }
    }

    /**
     * Returns the date this item hits EoS.
     * @return false|string
     */
    public function eos_date()
    {
        if (($this->model->model) && ($this->model->model->eos)) {
            return $this->model->model->eos->format('Y-m-d');
        }
    }

    /**
     * Returns the major firmware version.
     * @return false|string
     */
    public function firmware_major()
    {
        if (($this->firmware->major_release) && ($this->firmware->major_release)) {
            return $this->firmware->major_release;
        }
    }

    /**
     * Returns the minor firmware version.
     * @return false|string
     */
    public function firmware_minor()
    {
        if (($this->firmware->minor_release) && ($this->firmware->minor_release)) {
            return $this->firmware->minor_release;
        }
    }

    /**
     * @return string
     * This handles the status label "meta" status of "deployed" if
     * it's assigned. Should maybe deprecate.
     */
    public function statusMeta()
    {
        if ($this->model->assigned) {
            return 'deployed';
        }

        return $this->model->assetstatus->getStatuslabelType();
    }

    /**
     * @return string
     * This handles the status label "meta" status of "deployed" if
     * it's assigned. Should maybe deprecate.
     */
    public function statusText()
    {
        if ($this->model->assigned) {
            return trans('general.deployed');
        }

        return $this->model->assetstatus->name;
    }

    /**
     * Url to view this item.
     * @return string
     */
    public function viewUrl()
    {
        return route('hardware.show', $this->id);
    }

    public function glyph()
    {
        return '<i class="fas fa-barcode" aria-hidden="true"></i>';
    }
}
