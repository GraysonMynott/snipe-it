<?php

namespace App\Http\Transformers;

use App\Helpers\Helper;
use App\Models\Asset;
use App\Models\Setting;
use Illuminate\Support\Facades\Gate;
use Illuminate\Database\Eloquent\Collection;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class AssetsTransformer
{
    public function transformAssets(Collection $assets, $total)
    {
        $array = [];
        foreach ($assets as $asset) {
            $array[] = self::transformAsset($asset);
        }

        return (new DatatablesTransformer)->transformDatatables($array, $total);
    }

    public function transformAsset(Asset $asset)
    {
        // This uses the getSettings() method so we're pulling from the cache versus querying the settings on single asset
        $setting = Setting::getSettings();

        $array = [
            'id' => (int) $asset->id,
            'name' => e($asset->name),
            'asset_tag' => e($asset->asset_tag),
            'serial' => e($asset->serial),
            'model' => ($asset->model) ? [
                'id' => (int) $asset->model->id,
                'name'=> e($asset->model->name),
            ] : null,
            'model_number' => (($asset->model) && ($asset->model->model_number)) ? e($asset->model->model_number) : null,
            'eol' => (($asset->asset_eol_date != '') && ($asset->purchase_date != '')) ? Carbon::parse($asset->asset_eol_date)->diffInMonths($asset->purchase_date).' months' : null,
            'asset_eol_date' => ($asset->asset_eol_date != '') ? Helper::getFormattedDateObject($asset->asset_eol_date, 'date') : null,
            'status_label' => ($asset->assetstatus) ? [
                'id' => (int) $asset->assetstatus->id,
                'name'=> e($asset->assetstatus->name),
                'status_type'=> e($asset->assetstatus->getStatuslabelType()),
                'status_meta' => e($asset->present()->statusMeta),
            ] : null,
            'category' => (($asset->model) && ($asset->model->category)) ? [
                'id' => (int) $asset->model->category->id,
                'name'=> e($asset->model->category->name),
            ] : null,
            'manufacturer' => (($asset->model) && ($asset->model->manufacturer)) ? [
                'id' => (int) $asset->model->manufacturer->id,
                'name'=> e($asset->model->manufacturer->name),
            ] : null,
            'notes' => ($asset->notes) ? Helper::parseEscapedMarkedownInline($asset->notes) : null,
            'company' => ($asset->company) ? [
                'id' => (int) $asset->company->id,
                'name'=> e($asset->company->name),
            ] : null,
            'location' => ($asset->location) ? [
                'id' => (int) $asset->location->id,
                'name'=> e($asset->location->name),
            ] : null,
            'rtd_location' => ($asset->defaultLoc) ? [
                'id' => (int) $asset->defaultLoc->id,
                'name'=> e($asset->defaultLoc->name),
            ] : null,
            'image' => ($asset->getImageUrl()) ? $asset->getImageUrl() : null,
            'created_at' => Helper::getFormattedDateObject($asset->created_at, 'datetime'),
            'updated_at' => Helper::getFormattedDateObject($asset->updated_at, 'datetime'),
            'last_patch_date' => Helper::getFormattedDateObject($asset->last_patch_date, 'datetime'),
            'next_patch_date' => Helper::getFormattedDateObject($asset->next_patch_date, 'date'),
            'deleted_at' => Helper::getFormattedDateObject($asset->deleted_at, 'datetime'),
        ];


        if (($asset->model) && ($asset->model->fieldset) && ($asset->model->fieldset->fields->count() > 0)) {
            $fields_array = [];

            foreach ($asset->model->fieldset->fields as $field) {
                if ($field->isFieldDecryptable($asset->{$field->db_column})) {
                    $decrypted = Helper::gracefulDecrypt($field, $asset->{$field->db_column});
                    $value = (Gate::allows('assets.view.encrypted_custom_fields')) ? $decrypted : strtoupper(trans('admin/custom_fields/general.encrypted'));

                    if ($field->format == 'DATE'){
                        if (Gate::allows('assets.view.encrypted_custom_fields')){
                            $value = Helper::getFormattedDateObject($value, 'date', false);
                        } else {
                           $value = strtoupper(trans('admin/custom_fields/general.encrypted'));
                        }
                    }

                    $fields_array[$field->name] = [
                            'field' => e($field->db_column),
                            'value' => e($value),
                            'field_format' => $field->format,
                            'element' => $field->element,
                        ];

                } else {
                    $value = $asset->{$field->db_column};

                    if (($field->format == 'DATE') && (!is_null($value)) && ($value!='')){
                        $value = Helper::getFormattedDateObject($value, 'date', false);
                    }
                    
                    $fields_array[$field->name] = [
                        'field' => e($field->db_column),
                        'value' => e($value),
                        'field_format' => $field->format,
                        'element' => $field->element,
                    ];
                }

                $array['custom_fields'] = $fields_array;
            }
        } else {
            $array['custom_fields'] = new \stdClass; // HACK to force generation of empty object instead of empty list
        }

        $permissions_array['available_actions'] = [
            'clone'         => Gate::allows('create', Asset::class) ? true : false,
            'restore'       => ($asset->deleted_at!='' && Gate::allows('create', Asset::class)) ? true : false,
            'update'        => ($asset->deleted_at=='' && Gate::allows('update', Asset::class)) ? true : false,
            'delete'        => ($asset->deleted_at=='' && $asset->assigned_to =='' && Gate::allows('delete', Asset::class) && ($asset->deleted_at == '')) ? true : false,
        ];
        
        $array += $permissions_array;

        return $array;
    }

    public function transformAssetsDatatable($assets)
    {
        return (new DatatablesTransformer)->transformDatatables($assets);
    }


    public function transformRequestedAssets(Collection $assets, $total)
    {
        $array = [];
        foreach ($assets as $asset) {
            $array[] = self::transformRequestedAsset($asset);
        }

        return (new DatatablesTransformer)->transformDatatables($array, $total);
    }

    public function transformRequestedAsset(Asset $asset)
    {
        $array = [
            'id' => (int) $asset->id,
            'name' => e($asset->name),
            'asset_tag' => e($asset->asset_tag),
            'serial' => e($asset->serial),
            'image' => ($asset->getImageUrl()) ? $asset->getImageUrl() : null,
            'model' => ($asset->model) ? e($asset->model->name) : null,
            'model_number' => (($asset->model) && ($asset->model->model_number)) ? e($asset->model->model_number) : null,
            'expected_checkin' => Helper::getFormattedDateObject($asset->expected_checkin, 'date'),
            'location' => ($asset->location) ? e($asset->location->name) : null,
            'status'=> ($asset->assetstatus) ? $asset->present()->statusMeta : null,
            'assigned_to_self' => ($asset->assigned_to == auth()->id()),
        ];

        if (($asset->model) && ($asset->model->fieldset) && ($asset->model->fieldset->fields->count() > 0)) {
            $fields_array = [];

            foreach ($asset->model->fieldset->fields as $field) {

                // Only display this if it's allowed via the custom field setting
                if (($field->field_encrypted=='0') && ($field->show_in_requestable_list=='1')) {

                    $value = $asset->{$field->db_column};
                    if (($field->format == 'DATE') && (!is_null($value)) && ($value != '')) {
                        $value = Helper::getFormattedDateObject($value, 'date', false);
                    }

                    $fields_array[$field->db_column] = e($value);
                }

                $array['custom_fields'] = $fields_array;
            }
        } else {
            $array['custom_fields'] = new \stdClass; // HACK to force generation of empty object instead of empty list
        }


        $permissions_array['available_actions'] = [
            'cancel' => ($asset->isRequestedBy(auth()->user())) ? true : false,
            'request' => ($asset->isRequestedBy(auth()->user())) ? false : true,
        ];

        $array += $permissions_array;
        return $array;


    }
}
