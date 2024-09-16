<?php

namespace App\Http\Transformers;

use App\Helpers\Helper;
use App\Models\Location;
use Illuminate\Support\Facades\Gate;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Storage;

class LocationsTransformer
{
    public function transformLocations(Collection $locations, $total)
    {
        $array = [];
        foreach ($locations as $location) {
            $array[] = self::transformLocation($location);
        }

        return (new DatatablesTransformer)->transformDatatables($array, $total);
    }

    public function transformLocation(Location $location = null)
    {
        if ($location) {
            $array = [
                'id' => (int) $location->id,
                'name' => e($location->name),
                'address' =>  ($location->address) ? e($location->address) : null,
                'address2' =>  ($location->address2) ? e($location->address2) : null,
                'city' =>  ($location->city) ? e($location->city) : null,
                'state' =>  ($location->state) ? e($location->state) : null,
                'country' => ($location->country) ? e($location->country) : null,
                'zip' => ($location->zip) ? e($location->zip) : null,
                'phone' => ($location->phone!='') ? e($location->phone): null,
                'assets_count'    => (int) $location->assets_count,
                'created_at' => Helper::getFormattedDateObject($location->created_at, 'datetime'),
                'updated_at' => Helper::getFormattedDateObject($location->updated_at, 'datetime'),
            ];

            $permissions_array['available_actions'] = [
                'update' => Gate::allows('update', Location::class) ? true : false,
                'delete' => $location->isDeletable(),
                'bulk_selectable' => [
                    'delete' => $location->isDeletable()
                ],
                'clone' => (Gate::allows('create', Location::class) && ($location->deleted_at == '')),
            ];

            $array += $permissions_array;

            return $array;
        }
    }
}
