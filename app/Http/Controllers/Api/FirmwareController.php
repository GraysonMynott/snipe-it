<?php

namespace App\Http\Controllers\Api;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreFirmwareRequest;
use App\Http\Transformers\FirmwareTransformer;
use App\Http\Transformers\AssetsTransformer;
use App\Http\Transformers\SelectlistTransformer;
use App\Models\Asset;
use App\Models\Firmware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\JsonResponse;

/**
 * This class controls all actions related to firmware for
 * the Snipe-IT Asset Management application.
 *
 * @version    v4.0
 * @author [A. Gianotto] [<snipe@snipe.net>]
 */
class FirmwareController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @author [A. Gianotto] [<snipe@snipe.net>]
     * @since [v4.0]
     */
    public function index(Request $request) : JsonResponse | array
    {
        $this->authorize('view', Firmware::class);
        $allowed_columns =
            [
                'id',
                'name',
                'major_release',
                'minor_release',
                'recommended',
                'eol',
                'eos',
                'assets_count',
                'notes',
                'created_at',
                'manufacturer',
                'category',
            ];

        $firmwares = Firmware::select([
            'firmware.id',
            'firmware.name',
            'firmware.major_release',
            'firmware.minor_release',
            'firmware.recommended',
            'eol',
            'eos',
            'firmware.notes',
            'firmware.created_at',
            'category_id',
            'manufacturer_id',
            'firmware.deleted_at',
            'firmware.updated_at',
         ])
            ->with('category', 'manufacturer')
            ->withCount('assets as assets_count');

        if ($request->input('status')=='deleted') {
            $firmwares->onlyTrashed();
        }

        if ($request->filled('category_id')) {
            $firmwares = $firmwares->where('firmware.category_id', '=', $request->input('category_id'));
        }

        if ($request->filled('search')) {
            $firmwares->TextSearch($request->input('search'));
        }

        // Make sure the offset and limit are actually integers and do not exceed system limits
        $offset = ($request->input('offset') > $firmwares->count()) ? $firmwares->count() : abs($request->input('offset'));
        $limit = app('api_limit_value');

        $order = $request->input('order') === 'asc' ? 'asc' : 'desc';
        $sort = in_array($request->input('sort'), $allowed_columns) ? $request->input('sort') : 'firmware.created_at';

        switch ($sort) {
            case 'manufacturer':
                $firmwares->OrderManufacturer($order);
                break;
            case 'category':
                $firmwares->OrderCategory($order);
                break;
            default:
                $firmwares->orderBy($sort, $order);
                break;
        }

        $total = $firmwares->count();
        $firmwares = $firmwares->skip($offset)->take($limit)->get();

        return (new FirmwareTransformer)->transformFirmware($firmwares, $total);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @author [A. Gianotto] [<snipe@snipe.net>]
     * @since [v4.0]
     * @param  \App\Http\Requests\StoreFirmwareRequest  $request
     */
    public function store(StoreFirmwareRequest $request) : JsonResponse
    {
        $this->authorize('create', Firmware::class);
        $firmware = new Firmware;
        $firmware->fill($request->all());

        if ($firmware->save()) {
            return response()->json(Helper::formatStandardApiResponse('success', $firmware, trans('admin/firmware/message.create.success')));
        }
        return response()->json(Helper::formatStandardApiResponse('error', null, $firmware->getErrors()));


    }

    /**
     * Display the specified resource.
     *
     * @author [A. Gianotto] [<snipe@snipe.net>]
     * @since [v4.0]
     * @param  int  $id
     */
    public function show($id) :  array
    {
        $this->authorize('view', Firmware::class);
        $firmware = Firmware::withCount('assets as assets_count')->findOrFail($id);

        return (new FirmwareTransformer)->transformFirmware($firmware);
    }

    /**
     * Display the specified resource's assets
     *
     * @author [A. Gianotto] [<snipe@snipe.net>]
     * @since [v4.0]
     * @param  int  $id
     */
    public function assets($id) : array
    {
        $this->authorize('view', Firmware::class);
        $assets = Asset::where('firmware_id', '=', $id)->get();

        return (new AssetsTransformer)->transformAssets($assets, $assets->count());
    }


    /**
     * Update the specified resource in storage.
     *
     * @author [A. Gianotto] [<snipe@snipe.net>]
     * @since [v4.0]
     * @param  \App\Http\Requests\StoreFirmwareRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(StoreFirmwareRequest $request, $id) : JsonResponse
    {
        $this->authorize('update', Firmware::class);
        $firmware = Firmware::findOrFail($id);
        $firmware->fill($request->all());
        $firmware = $request->handleImages($firmware);

        if ($firmware->save()) {
            return response()->json(Helper::formatStandardApiResponse('success', $firmware, trans('admin/firmware/message.update.success')));
        }

        return response()->json(Helper::formatStandardApiResponse('error', null, $firmware->getErrors()));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @author [A. Gianotto] [<snipe@snipe.net>]
     * @since [v4.0]
     * @param  int  $id
     */
    public function destroy($id) : JsonResponse
    {
        $this->authorize('delete', Firmware::class);
        $firmware = Firmware::findOrFail($id);
        $this->authorize('delete', $firmware);

        if ($firmware->assets()->count() > 0) {
            return response()->json(Helper::formatStandardApiResponse('error', null, trans('admin/firmware/message.assoc_users')));
        }

        $firmware->delete();

        return response()->json(Helper::formatStandardApiResponse('success', null, trans('admin/firmware/message.delete.success')));
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
        $this->authorize('view.selectlists');
        $firmware_array = Firmware::select([
            'firmware.id',
            'firmware.name',
            'firmware.major_release',
            'firmware.manufacturer_id',
            'firmware.category_id',
        ])->with('manufacturer', 'category');

        $settings = \App\Models\Setting::getSettings();

        if ($request->filled('search')) {
            $firmware_array = $firmware_array->SearchByManufacturerOrCat($request->input('search'));
        }

        $firmware_array = $firmware_array->OrderCategory('ASC')->OrderManufacturer('ASC')->orderby('firmware.name', 'asc')->orderby('firmware.major_release', 'asc')->paginate(50);

        foreach ($firmware_array as $firmware) {
            $firmware->use_text = '';

            if ($settings->modellistCheckedValue('category')) {
                $firmware->use_text .= (($firmware->category) ? $firmware->category->name.' - ' : '');
            }

            if ($settings->modellistCheckedValue('manufacturer')) {
                $firmware->use_text .= (($firmware->manufacturer) ? $firmware->manufacturer->name.' ' : '');
            }

            $firmware->use_text .= $firmware->name;
        }

        return (new SelectlistTransformer)->transformSelectlist($firmware_array);
    }
}
