<?php

namespace App\Http\Controllers\Api;

use App\Helpers\Helper;
use App\Http\Requests\ImageUploadRequest;
use App\Http\Controllers\Controller;
use App\Http\Transformers\LocationsTransformer;
use App\Http\Transformers\SelectlistTransformer;
use App\Models\Location;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Http\JsonResponse;

class LocationsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @author [A. Gianotto] [<snipe@snipe.net>]
     * @since [v4.0]
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request) : JsonResponse | array
    {
        $this->authorize('view', Location::class);
        $allowed_columns = [
            'id',
            'name',
            'address',
            'address2',
            'city',
            'state',
            'country',
            'zip',
            'created_at',
            'updated_at',
            'assets_count',
            ];

        $locations = Location::select([
            'locations.id',
            'locations.name',
            'locations.address',
            'locations.address2',
            'locations.city',
            'locations.state',
            'locations.zip',
            'locations.phone',
            'locations.country',
            'locations.created_at',
            'locations.updated_at',
        ])->withCount('assets as assets_count');

        if ($request->filled('search')) {
            $locations = $locations->TextSearch($request->input('search'));
        }

        if ($request->filled('name')) {
            $locations->where('locations.name', '=', $request->input('name'));
        }

        if ($request->filled('address')) {
            $locations->where('locations.address', '=', $request->input('address'));
        }

        if ($request->filled('address2')) {
            $locations->where('locations.address2', '=', $request->input('address2'));
        }

        if ($request->filled('city')) {
            $locations->where('locations.city', '=', $request->input('city'));
        }

        if ($request->filled('zip')) {
            $locations->where('locations.zip', '=', $request->input('zip'));
        }

        if ($request->filled('country')) {
            $locations->where('locations.country', '=', $request->input('country'));
        }

        // Make sure the offset and limit are actually integers and do not exceed system limits
        $offset = ($request->input('offset') > $locations->count()) ? $locations->count() : app('api_offset_value');
        $limit = app('api_limit_value');

        $order = $request->input('order') === 'asc' ? 'asc' : 'desc';
        $sort = in_array($request->input('sort'), $allowed_columns) ? $request->input('sort') : 'created_at';



        switch ($request->input('sort')) {
            default:
                $locations->orderBy($sort, $order);
                break;
        }

        $total = $locations->count();
        $locations = $locations->skip($offset)->take($limit)->get();

        return (new LocationsTransformer)->transformLocations($locations, $total);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @author [A. Gianotto] [<snipe@snipe.net>]
     * @since [v4.0]
     * @param  \App\Http\Requests\ImageUploadRequest  $request
     */
    public function store(ImageUploadRequest $request) : JsonResponse
    {
        $this->authorize('create', Location::class);
        $location = new Location;
        $location->fill($request->all());
        $location = $request->handleImages($location);

        if ($location->save()) {
            return response()->json(Helper::formatStandardApiResponse('success', (new LocationsTransformer)->transformLocation($location), trans('admin/locations/message.create.success')));
        }

        return response()->json(Helper::formatStandardApiResponse('error', null, $location->getErrors()));
    }

    /**
     * Display the specified resource.
     *
     * @author [A. Gianotto] [<snipe@snipe.net>]
     * @since [v4.0]
     * @param  int  $id
     */
    public function show($id) : JsonResponse | array
    {
        $this->authorize('view', Location::class);
        $location = Location::select([
                'locations.id',
                'locations.name',
                'locations.address',
                'locations.address2',
                'locations.city',
                'locations.state',
                'locations.zip',
                'locations.country',
                'locations.created_at',
                'locations.updated_at',
            ])->withCount('assets as assets_count')
            ->findOrFail($id);

        return (new LocationsTransformer)->transformLocation($location);
    }


    /**
     * Update the specified resource in storage.
     *
     * @author [A. Gianotto] [<snipe@snipe.net>]
     * @since [v4.0]
     * @param  \App\Http\Requests\ImageUploadRequest  $request
     * @param  int  $id
     */
    public function update(ImageUploadRequest $request, $id) : JsonResponse
    {
        $this->authorize('update', Location::class);
        $location = Location::findOrFail($id);

        $location->fill($request->all());
        $location = $request->handleImages($location);

        if ($location->isValid()) {

            $location->save();
            return response()->json(
                Helper::formatStandardApiResponse(
                    'success',
                    (new LocationsTransformer)->transformLocation($location),
                    trans('admin/locations/message.update.success')
                )
            );
        }

        return response()->json(Helper::formatStandardApiResponse('error', null, $location->getErrors()));
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
        $this->authorize('delete', Location::class);
        $location = Location::withCount('assets as assets_count')
            ->findOrFail($id);

        if (! $location->isDeletable()) {
            return response()
                    ->json(Helper::formatStandardApiResponse('error', null, trans('admin/locations/message.assoc_users')));
        }
        $this->authorize('delete', $location);
        $location->delete();

        return response()->json(Helper::formatStandardApiResponse('success', null, trans('admin/locations/message.delete.success')));
    }

    /**
     * Gets a paginated collection for the select2 menus
     *
     * This is handled slightly differently as of ~4.7.8-pre, as
     * we have to do some recursive magic to get the hierarchy to display
     * properly when looking at the parent/child relationship in the
     * rich menus.
     *
     * This means we can't use the normal pagination that we use elsewhere
     * in our selectlists, since we have to get the full set before we can
     * determine which location is parent/child/grandchild, etc.
     *
     * This also means that hierarchy display gets a little funky when people
     * use the Select2 search functionality, but there's not much we can do about
     * that right now.
     *
     * As a result, instead of paginating as part of the query, we have to grab
     * the entire data set, and then invoke a paginator manually and pass that
     * through to the SelectListTransformer.
     *
     * Many thanks to @uberbrady for the help getting this working better.
     * Recursion still sucks, but I guess he doesn't have to get in the
     * sea... this time.
     *
     * @author [A. Gianotto] [<snipe@snipe.net>]
     * @since [v4.0.16]
     * @see \App\Http\Transformers\SelectlistTransformer
     */
    public function selectlist(Request $request) : array
    {
        // If a user is in the process of editing their profile, as determined by the referrer,
        // then we check that they have permission to edit their own location.
        // Otherwise, we do our normal check that they can view select lists.
        $request->headers->get('referer') === route('profile')
            ? $this->authorize('self.edit_location')
            : $this->authorize('view.selectlists');

        $locations = Location::select([
            'locations.id',
            'locations.name',
        ]);

        $page = 1;
        if ($request->filled('page')) {
            $page = $request->input('page');
        }

        if ($request->filled('search')) {
            $locations = $locations->where('locations.name', 'LIKE', '%'.$request->input('search').'%');
        }

        $locations = $locations->orderBy('name', 'ASC')->get();

        $paginated_results = new LengthAwarePaginator($locations->forPage($page, 500), $locations->count(), 500, $page, []);

        return (new SelectlistTransformer)->transformSelectlist($paginated_results);
    }
}
