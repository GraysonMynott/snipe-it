<?php
namespace App\Http\Transformers;

use App\Helpers\Helper;
use App\Models\Asset;
use Illuminate\Database\Eloquent\Collection;

/**
 *  This tranformer looks like it's extraneous, since we return as much or more
 * info in the AssetsTransformer, but we want to flatten these results out so that they 
 * don't dislose more information than we want. Folks with depreciation powers don't necessaily 
 * have the right to see additional info, and inspecting the API call here could disclose 
 * info they're not supposed to see.
 * 
 * @author [A. Gianotto] [<snipe@snipe.net>]
 * @since [v5.2.0]
 */
class DepreciationReportTransformer
{
    public function transformAssets(Collection $assets, $total)
    {
        $array = array();
        foreach ($assets as $asset) {
            $array[] = self::transformAsset($asset);
        }
        return (new DatatablesTransformer)->transformDatatables($array, $total);
    }


    public function transformAsset(Asset $asset)
    {

        $array = [
    
            'company' => ($asset->company) ? e($asset->company->name) : null,
            'name' => e($asset->name),
            'asset_tag' => e($asset->asset_tag),
            'serial' => e($asset->serial),
            'model' => ($asset->model) ?  e($asset->model->name) : null,
            'model_number' => (($asset->model) && ($asset->model->model_number)) ? e($asset->model->model_number) : null,
            'eol' => ($asset->purchase_date!='') ? Helper::getFormattedDateObject($asset->present()->eol_date(), 'date') : null ,
            'status_label' => ($asset->assetstatus) ? e($asset->assetstatus->name) : null,
            'status' => ($asset->assetstatus) ?  e($asset->present()->statusMeta) : null,
            'category' => (($asset->model) && ($asset->model->category)) ? e($asset->model->category->name) : null,
            'manufacturer' => (($asset->model) && ($asset->model->manufacturer)) ? e($asset->model->manufacturer->name) : null,
            'notes' => ($asset->notes) ? e($asset->notes) : null,
            'location' => ($asset->location) ? e($asset->location->name) : null,
            
        ];

        return $array;
    }

    public function transformAssetsDatatable($assets)
    {
        return (new DatatablesTransformer)->transformDatatables($assets);
    }


   
}
