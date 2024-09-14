<?php

namespace App\Http\Controllers\Api;

use App\Events\CheckoutableCheckedIn;
use App\Http\Requests\StoreAssetRequest;
use App\Http\Traits\MigratesLegacyAssetLocations;
use App\Models\CheckoutAcceptance;
use App\Models\LicenseSeat;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Gate;
use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Http\Requests\AssetCheckoutRequest;
use App\Http\Transformers\AssetsTransformer;
use App\Http\Transformers\LicensesTransformer;
use App\Http\Transformers\SelectlistTransformer;
use App\Models\Asset;
use App\Models\AssetModel;
use App\Models\Company;
use App\Models\CustomField;
use App\Models\License;
use App\Models\Location;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Http\Requests\ImageUploadRequest;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;


/**
 * This class controls all actions related to assets for
 * the Snipe-IT Asset Management application.
 *
 * @version    v1.0
 * @author [A. Gianotto] [<snipe@snipe.net>]
 */
class AssetsController extends Controller
{
    use MigratesLegacyAssetLocations;

    /**
     * Returns JSON listing of all assets
     *
     * @author [A. Gianotto] [<snipe@snipe.net>]
     * @param int $assetId
     * @since [v4.0]
     */
    public function index(Request $request, $action = null, $upcoming_status = null) : JsonResponse | array
    {

        $filter_non_deprecable_assets = false;

        /**
         * This looks MAD janky (and it is), but the AssetsController@index does a LOT of heavy lifting throughout the 
         * app. This bit here just makes sure that someone without permission to view assets doesn't 
         * end up with priv escalations because they asked for a different endpoint. 
         * 
         * Since we never gave the specification for which transformer to use before, it should default 
         * gracefully to just use the AssetTransformer by default, which shouldn't break anything. 
         * 
         * It was either this mess, or repeating ALL of the searching and sorting and filtering code, 
         * which would have been far worse of a mess. *sad face*  - snipe (Sept 1, 2021)
         */
        if (Route::currentRouteName()=='api.asset-report.index') {
            $filter_non_deprecable_assets = true;
            $transformer = 'App\Http\Transformers\DepreciationReportTransformer';
            $this->authorize('reports.view');
        } else {
            $transformer = 'App\Http\Transformers\AssetsTransformer';
            $this->authorize('index', Asset::class);          
        }
        
       
        $settings = Setting::getSettings();

        $allowed_columns = [
            'id',
            'name',
            'asset_tag',
            'serial',
            'model_number',
            'notes',
            'image',
            'created_at',
            'updated_at',
            'last_patch_date',
            'next_patch_date',
            'asset_eol_date',
        ];

        $filter = [];

        if ($request->filled('filter')) {
            $filter = json_decode($request->input('filter'), true);
        }

        $all_custom_fields = CustomField::all(); //used as a 'cache' of custom fields throughout this page load
        foreach ($all_custom_fields as $field) {
            $allowed_columns[] = $field->db_column_name();
        }

        $assets = Asset::select('assets.*')
            ->with('location', 'assetstatus', 'company', 'defaultLoc',
                'model.category', 'model.manufacturer', 'model.fieldset'); //it might be tempting to add 'assetlog' here, but don't. It blows up update-heavy users.


        // These are used by the API to query against specific ID numbers.
        // They are also used by the individual searches on detail pages like
        // locations, etc.

        // Search custom fields by column name
        foreach ($all_custom_fields as $field) {
            if ($request->filled($field->db_column_name()) && $field->db_column_name()) {
                $assets->where($field->db_column_name(), '=', $request->input($field->db_column_name()));
            }
        }

        if ((! is_null($filter)) && (count($filter)) > 0) {
            $assets->ByFilter($filter);
        } elseif ($request->filled('search')) {
            $assets->TextSearch($request->input('search'));
        }


        /**
         * Handle due and overdue patches and checkin dates
         */
        switch ($action) {
            case 'patches':

                switch ($upcoming_status) {
                    case 'due':
                        $assets->DueForPatch($settings);
                        break;
                    case 'overdue':
                        $assets->OverdueForPatch();
                        break;
                    case 'due-or-overdue':
                        $assets->DueOrOverdueForPatch($settings);
                        break;
                }
                break;

            case 'checkins':
                switch ($upcoming_status) {
                    case 'due':
                        $assets->DueForCheckin($settings);
                        break;
                    case 'overdue':
                        $assets->OverdueForCheckin();
                        break;
                    case 'due-or-overdue':
                        $assets->DueOrOverdueForCheckin($settings);
                        break;
                }
                break;
            }

        /**
         * End handling due and overdue patches and checkin dates
         */


        // This is used by the sidenav, mostly

        // We switched from using query scopes here because of a Laravel bug
        // related to fulltext searches on complex queries.
        // I am sad. :(
        switch ($request->input('status')) {
            case 'Deleted':
                $assets->onlyTrashed();
                break;
            case 'Pending':
                $assets->join('status_labels AS status_alias', function ($join) {
                    $join->on('status_alias.id', '=', 'assets.status_id')
                        ->where('status_alias.deployable', '=', 0)
                        ->where('status_alias.pending', '=', 1)
                        ->where('status_alias.archived', '=', 0);
                });
                break;
/*             case 'RTD':
                $assets->whereNull('assets.assigned_to')
                    ->join('status_labels AS status_alias', function ($join) {
                        $join->on('status_alias.id', '=', 'assets.status_id')
                            ->where('status_alias.deployable', '=', 1)
                            ->where('status_alias.pending', '=', 0)
                            ->where('status_alias.archived', '=', 0);
                    });
                break; */
            case 'Undeployable':
                $assets->Undeployable();
                break;
            case 'Archived':
                $assets->join('status_labels AS status_alias', function ($join) {
                    $join->on('status_alias.id', '=', 'assets.status_id')
                        ->where('status_alias.deployable', '=', 0)
                        ->where('status_alias.pending', '=', 0)
                        ->where('status_alias.archived', '=', 1);
                });
                break;
            case 'Deployed':
//                // more sad, horrible workarounds for laravel bugs when doing full text searches
//                $assets->whereNotNull('assets.assigned_to');
//                break;
            default:

                if ((! $request->filled('status_id')) && ($settings->show_archived_in_list != '1')) {
                    // terrible workaround for complex-query Laravel bug in fulltext
                    $assets->join('status_labels AS status_alias', function ($join) {
                        $join->on('status_alias.id', '=', 'assets.status_id')
                            ->where('status_alias.archived', '=', 0);
                    });

                    // If there is a status ID, don't take show_archived_in_list into consideration
                } else {
                    $assets->join('status_labels AS status_alias', function ($join) {
                        $join->on('status_alias.id', '=', 'assets.status_id');
                    });
                }

        }


        // Leave these under the TextSearch scope, else the fuzziness will override the specific ID (status ID, etc) requested
        if ($request->filled('status_id')) {
            $assets->where('assets.status_id', '=', $request->input('status_id'));
        }

        if ($request->filled('asset_tag')) {
            $assets->where('assets.asset_tag', '=', $request->input('asset_tag'));
        }

        if ($request->filled('serial')) {
            $assets->where('assets.serial', '=', $request->input('serial'));
        }

        if ($request->filled('model_id')) {
            $assets->InModelList([$request->input('model_id')]);
        }

        if ($request->filled('category_id')) {
            $assets->InCategory($request->input('category_id'));
        }

        if ($request->filled('location_id')) {
            $assets->where('assets.location_id', '=', $request->input('location_id'));
        }

        if ($request->filled('rtd_location_id')) {
            $assets->where('assets.rtd_location_id', '=', $request->input('rtd_location_id'));
        }

        if ($request->filled('asset_eol_date')) {
            $assets->where('assets.asset_eol_date', '=', $request->input('asset_eol_date'));
        }

        if ($request->filled('company_id')) {
            $assets->where('assets.company_id', '=', $request->input('company_id'));
        }

        if ($request->filled('manufacturer_id')) {
            $assets->ByManufacturer($request->input('manufacturer_id'));
        }

        // This is kinda gross, but we need to do this because the Bootstrap Tables
        // API passes custom field ordering as custom_fields.fieldname, and we have to strip
        // that out to let the default sorter below order them correctly on the assets table.
        $sort_override = str_replace('custom_fields.', '', $request->input('sort'));

        // This handles all of the pivot sorting (versus the assets.* fields
        // in the allowed_columns array)
        $column_sort = in_array($sort_override, $allowed_columns) ? $sort_override : 'assets.created_at';

        $order = $request->input('order') === 'asc' ? 'asc' : 'desc';
        
        switch ($sort_override) {
            case 'model':
                $assets->OrderModels($order);
                break;
            case 'model_number':
                $assets->OrderModelNumber($order);
                break;
            case 'category':
                $assets->OrderCategory($order);
                break;
            case 'manufacturer':
                $assets->OrderManufacturer($order);
                break;
            case 'company':
                $assets->OrderCompany($order);
                break;
            case 'location':
                $assets->OrderLocation($order);
            case 'rtd_location':
                $assets->OrderRtdLocation($order);
                break;
            case 'status_label':
                $assets->OrderStatus($order);
                break;
            default:
                $assets->orderBy($column_sort, $order);
                break;
        }


        // Make sure the offset and limit are actually integers and do not exceed system limits
        $offset = ($request->input('offset') > $assets->count()) ? $assets->count() : app('api_offset_value');
        $limit = app('api_limit_value');

        $total = $assets->count();
        $assets = $assets->skip($offset)->take($limit)->get();



        /**
         * Here we're just determining which Transformer (via $transformer) to use based on the 
         * variables we set earlier on in this method - we default to AssetsTransformer.
         */
        return (new $transformer)->transformAssets($assets, $total, $request);
    }


    /**
     * Returns JSON with information about an asset (by tag) for detail view.
     *
     * @param string $tag
     * @since [v4.2.1]
     * @author [A. Gianotto] [<snipe@snipe.net>]
     */
    public function showByTag(Request $request, $tag) : JsonResponse | array
    {
        $this->authorize('index', Asset::class);
        $assets = Asset::where('asset_tag', $tag)->with('assetstatus');

        // Check if they've passed ?deleted=true
        if ($request->input('deleted', 'false') == 'true') {
            $assets = $assets->withTrashed();
        }

        if (($assets = $assets->get()) && ($assets->count()) > 0) {

            // If there is exactly one result and the deleted parameter is not passed, we should pull the first (and only)
            // asset from the returned collection, since transformAsset() expects an Asset object, NOT a collection
            if (($assets->count() == 1) && ($request->input('deleted') != 'true')) {
                return (new AssetsTransformer)->transformAsset($assets->first());

                // If there is more than one result OR if the endpoint is requesting deleted items (even if there is only one
                // match, return the normal collection transformed.
            } else {
                return (new AssetsTransformer)->transformAssets($assets, $assets->count());
            }

        }

        // If there are 0 results, return the "no such asset" response
        return response()->json(Helper::formatStandardApiResponse('error', null, trans('admin/hardware/message.does_not_exist')), 200);

    }

    /**
     * Returns JSON with information about an asset (by serial) for detail view.
     *
     * @author [A. Gianotto] [<snipe@snipe.net>]
     * @param string $serial
     * @since [v4.2.1]
     * @return \Illuminate\Http\JsonResponse
     */
    public function showBySerial(Request $request, $serial) : JsonResponse | array
    {
        $this->authorize('index', Asset::class);
        $assets = Asset::where('serial', $serial)->with('assetstatus');

        // Check if they've passed ?deleted=true
        if ($request->input('deleted', 'false') == 'true') {
            $assets = $assets->withTrashed();
        }
        
        if (($assets = $assets->get()) && ($assets->count()) > 0) {
             return (new AssetsTransformer)->transformAssets($assets, $assets->count());
        }

        // If there are 0 results, return the "no such asset" response
        return response()->json(Helper::formatStandardApiResponse('error', null, trans('admin/hardware/message.does_not_exist')), 200);

    }

    /**
     * Returns JSON with information about an asset for detail view.
     *
     * @author [A. Gianotto] [<snipe@snipe.net>]
     * @param int $assetId
     * @since [v4.0]
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Request $request, $id) : JsonResponse | array
    {
        if ($asset = Asset::with('assetstatus')
            ->withTrashed()
            ->withCount('checkins as checkins_count', 'checkouts as checkouts_count', 'userRequests as user_requests_count')->find($id)) {
            $this->authorize('view', $asset);

            return (new AssetsTransformer)->transformAsset($asset, $request->input('components') );
        }
        return response()->json(Helper::formatStandardApiResponse('error', null, trans('admin/hardware/message.does_not_exist')), 200);

    }

    public function licenses(Request $request, $id) : array
    {
        $this->authorize('view', Asset::class);
        $this->authorize('view', License::class);
        $asset = Asset::where('id', $id)->withTrashed()->firstorfail();
        $licenses = $asset->licenses()->get();

        return (new LicensesTransformer())->transformLicenses($licenses, $licenses->count());
     }


    /**
     * Gets a paginated collection for the select2 menus
     *
     * @author [A. Gianotto] [<snipe@snipe.net>]
     * @since [v4.0.16]
     * @see \App\Http\Transformers\SelectlistTransformer
     */
    public function selectlist(Request $request) : array
    {

        $assets = Asset::select([
            'assets.id',
            'assets.name',
            'assets.asset_tag',
            'assets.model_id',
            'assets.status_id',
            ])->with('model', 'assetstatus')->NotArchived();

        if ($request->filled('assetStatusType') && $request->input('assetStatusType') === 'RTD') {
            $assets = $assets->RTD();
        }

        if ($request->filled('search')) {
            $assets = $assets->AssignedSearch($request->input('search'));
        }


        $assets = $assets->paginate(50);

        // Loop through and set some custom properties for the transformer to use.
        // This lets us have more flexibility in special cases like assets, where
        // they may not have a ->name value but we want to display something anyway
        foreach ($assets as $asset) {


            $asset->use_text = $asset->present()->fullName;


            if ($asset->assetstatus->getStatuslabelType() == 'pending') {
                $asset->use_text .= '('.$asset->assetstatus->getStatuslabelType().')';
            }

            $asset->use_image = ($asset->getImageUrl()) ? $asset->getImageUrl() : null;
        }

        return (new SelectlistTransformer)->transformSelectlist($assets);
    }


    /**
     * Accepts a POST request to create a new asset
     *
     * @author [A. Gianotto] [<snipe@snipe.net>]
     * @param \App\Http\Requests\ImageUploadRequest $request
     * @since [v4.0]
     */
    public function store(StoreAssetRequest $request): JsonResponse
    {
        $asset = new Asset();
        $asset->model()->associate(AssetModel::find((int) $request->get('model_id')));

        $asset->fill($request->validated());
        $asset->user_id    = Auth::id();

        /**
        * this is here just legacy reasons. Api\AssetController
        * used image_source  once to allow encoded image uploads.
        */
        if ($request->has('image_source')) {
            $request->offsetSet('image', $request->offsetGet('image_source'));
        }     

        $asset = $request->handleImages($asset);

        // Update custom fields in the database.
        $model = AssetModel::find($request->input('model_id'));

        // Check that it's an object and not a collection
        // (Sometimes people send arrays here and they shouldn't
        if (($model) && ($model instanceof AssetModel) && ($model->fieldset)) {
            foreach ($model->fieldset->fields as $field) {

                // Set the field value based on what was sent in the request
                $field_val = $request->input($field->db_column, null);

                // If input value is null, use custom field's default value
                if ($field_val == null) {
                    Log::debug('Field value for '.$field->db_column.' is null');
                    $field_val = $field->defaultValue($request->get('model_id'));
                    Log::debug('Use the default fieldset value of '.$field->defaultValue($request->get('model_id')));
                }

                // if the field is set to encrypted, make sure we encrypt the value
                if ($field->field_encrypted == '1') {
                    Log::debug('This model field is encrypted in this fieldset.');

                    if (Gate::allows('admin')) {

                        // If input value is null, use custom field's default value
                        if (($field_val == null) && ($request->has('model_id') != '')) {
                            $field_val = Crypt::encrypt($field->defaultValue($request->get('model_id')));
                        } else {
                            $field_val = Crypt::encrypt($request->input($field->db_column));
                        }
                    }
                }
                if ($field->element == 'checkbox') {
                    if(is_array($field_val)) {
                        $field_val = implode(',', $field_val);
                    }
                }


                $asset->{$field->db_column} = $field_val;
            }
        }

        if ($asset->save()) {
            if ($asset->image) {
                $asset->image = $asset->getImageUrl();
            }

            return response()->json(Helper::formatStandardApiResponse('success', $asset, trans('admin/hardware/message.create.success')));

//            return response()->json(Helper::formatStandardApiResponse('success', (new AssetsTransformer)->transformAsset($asset), trans('admin/hardware/message.create.success')));
        }

        return response()->json(Helper::formatStandardApiResponse('error', null, $asset->getErrors()), 200);
    }


    /**
     * Accepts a POST request to update an asset
     *
     * @author [A. Gianotto] [<snipe@snipe.net>]
     * @param \App\Http\Requests\ImageUploadRequest $request
     * @since [v4.0]
     */
    public function update(ImageUploadRequest $request, $id) : JsonResponse
    {
        $this->authorize('update', Asset::class);

        if ($asset = Asset::find($id)) {
            $asset->fill($request->all());

            ($request->filled('model_id')) ?
                $asset->model()->associate(AssetModel::find($request->get('model_id'))) : null;
            ($request->filled('rtd_location_id')) ?
                $asset->location_id = $request->get('rtd_location_id') : '';
            ($request->filled('company_id')) ?
                $asset->company_id = Company::getIdForCurrentUser($request->get('company_id')) : '';

            ($request->filled('rtd_location_id')) ?
                $asset->location_id = $request->get('rtd_location_id') : null;

            /**
            * this is here just legacy reasons. Api\AssetController
            * used image_source  once to allow encoded image uploads.
            */
            if ($request->has('image_source')) {
                $request->offsetSet('image', $request->offsetGet('image_source'));
            }     

            $asset = $request->handleImages($asset);
            $model = AssetModel::find($asset->model_id);
            
            // Update custom fields
            $problems_updating_encrypted_custom_fields = false;
            if (($model) && (isset($model->fieldset))) {
                foreach ($model->fieldset->fields as $field) {
                    $field_val = $request->input($field->db_column, null);

                    if ($request->has($field->db_column)) {
                        if ($field->element == 'checkbox') {
                            if(is_array($field_val)) {
                                $field_val = implode(',', $field_val);
                            }
                        }
                        if ($field->field_encrypted == '1') {
                            if (Gate::allows('admin')) {
                                $field_val = Crypt::encrypt($field_val);
                            } else {
                                $problems_updating_encrypted_custom_fields = true;
                                continue;
                            }
                        }
                        $asset->{$field->db_column} = $field_val;
                    }
                }
            }


            if ($asset->save()) {
                if ($asset->image) {
                    $asset->image = $asset->getImageUrl();
                }

                if ($problems_updating_encrypted_custom_fields) {
                    return response()->json(Helper::formatStandardApiResponse('success', $asset, trans('admin/hardware/message.update.encrypted_warning')));
                } else {
                    return response()->json(Helper::formatStandardApiResponse('success', $asset, trans('admin/hardware/message.update.success')));
                }
            }

            return response()->json(Helper::formatStandardApiResponse('error', null, $asset->getErrors()), 200);
        }

        return response()->json(Helper::formatStandardApiResponse('error', null, trans('admin/hardware/message.does_not_exist')), 200);
    }


    /**
     * Delete a given asset (mark as deleted).
     *
     * @author [A. Gianotto] [<snipe@snipe.net>]
     * @param int $assetId
     * @since [v4.0]
     */
    public function destroy($id) : JsonResponse
    {
        $this->authorize('delete', Asset::class);

        if ($asset = Asset::find($id)) {
            $this->authorize('delete', $asset);

            $asset->delete();

            return response()->json(Helper::formatStandardApiResponse('success', null, trans('admin/hardware/message.delete.success')));
        }

        return response()->json(Helper::formatStandardApiResponse('error', null, trans('admin/hardware/message.does_not_exist')), 200);
    }

    

    /**
     * Restore a soft-deleted asset.
     *
     * @author [A. Gianotto] [<snipe@snipe.net>]
     * @param int $assetId
     * @since [v5.1.18]
     */
    public function restore(Request $request, $assetId = null) : JsonResponse
    {

        if ($asset = Asset::withTrashed()->find($assetId)) {
            $this->authorize('delete', $asset);

            if ($asset->deleted_at == '') {
                return response()->json(Helper::formatStandardApiResponse('error', trans('general.not_deleted', ['item_type' => trans('general.asset')])), 200);
            }

            if ($asset->restore()) {
                return response()->json(Helper::formatStandardApiResponse('success', trans('admin/hardware/message.restore.success')), 200);
            }

            // Check validation to make sure we're not restoring an asset with the same asset tag (or unique attribute) as an existing asset
            return response()->json(Helper::formatStandardApiResponse('error', trans('general.could_not_restore', ['item_type' => trans('general.asset'), 'error' => $asset->getErrors()->first()])), 200);
        }

        return response()->json(Helper::formatStandardApiResponse('error', null, trans('admin/hardware/message.does_not_exist')), 200);

    }


    /**
     * Mark an asset as patched
     *
     * @author [A. Gianotto] [<snipe@snipe.net>]
     * @param int $id
     * @since [v4.0]
     */
    public function patch(Request $request) : JsonResponse

    {
        $this->authorize('patch', Asset::class);

        $settings = Setting::getSettings();
        $dt = Carbon::now()->addMonths($settings->patch_interval)->toDateString();

        // No tag passed - return an error
        if (!$request->filled('asset_tag')) {
            return response()->json(Helper::formatStandardApiResponse('error', [
                'asset_tag'=> '',
                'error'=> trans('admin/hardware/message.no_tag'),
            ], trans('admin/hardware/message.no_tag')), 200);
        }


        $asset = Asset::where('asset_tag', '=', $request->input('asset_tag'))->first();


        if ($asset) {

            /**
             * Even though we do a save() further down, we don't want to log this as a "normal" asset update,
             * which would trigger the Asset Observer and would log an asset *update* log entry (because the
             * de-normed fields like next_patch_date on the asset itself will change on save()) *in addition* to
             * the patch log entry we're creating through this controller.
             *
             * To prevent this double-logging (one for update and one for patch), we skip the observer and bypass
             * that de-normed update log entry by using unsetEventDispatcher(), BUT invoking unsetEventDispatcher()
             * will bypass normal model-level validation that's usually handled at the observer )
             *
             * We handle validation on the save() by checking if the asset is valid via the ->isValid() method,
             * which manually invokes Watson Validating to make sure the asset's model is valid.
             *
             * @see \App\Observers\AssetObserver::updating()
             */
            $asset->unsetEventDispatcher();
            $asset->next_patch_date = $dt;

            if ($request->filled('next_patch_date')) {
                $asset->next_patch_date = $request->input('next_patch_date');
            }

            // Check to see if they checked the box to update the physical location,
            // not just note it in the patch notes
            if ($request->input('update_location') == '1') {
                $asset->location_id = $request->input('location_id');
            }

            $asset->last_patch_date = date('Y-m-d H:i:s');

            /**
             * Invoke Watson Validating to check the asset itself and check to make sure it saved correctly.
             * We have to invoke this manually because of the unsetEventDispatcher() above.)
             */
            if ($asset->isValid() && $asset->save()) {
                $asset->logPatch(request('note'), request('location_id'));

                return response()->json(Helper::formatStandardApiResponse('success', [
                    'asset_tag'=> e($asset->asset_tag),
                    'note'=> e($request->input('note')),
                    'next_patch_date' => Helper::getFormattedDateObject($asset->next_patch_date),
                ], trans('admin/hardware/message.patch.success')));
            }

            // Asset failed validation or was not able to be saved
            return response()->json(Helper::formatStandardApiResponse('error', [
                'asset_tag'=> e($asset->asset_tag),
                'error'=> $asset->getErrors()->first(),
            ], trans('admin/hardware/message.patch.error', ['error' => $asset->getErrors()->first()])), 200);

        }


        // No matching asset for the asset tag that was passed.
        return response()->json(Helper::formatStandardApiResponse('error', [
            'asset_tag'=> e($request->input('asset_tag')),
            'error'=> trans('admin/hardware/message.patch.error'),
        ], trans('admin/hardware/message.patch.error', ['error' => trans('admin/hardware/message.does_not_exist')])), 200);


    }
}
