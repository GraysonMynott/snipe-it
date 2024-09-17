<?php

namespace App\Http\Transformers;

use App\Helpers\Helper;
use App\Models\Firmware;
use Illuminate\Support\Facades\Gate;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Storage;

class FirmwareTransformer
{
    public function transformFirmware(Collection $firmware_array, $total)
    {
        $array = [];
        foreach ($firmware_array as $firmware) {
            $array[] = self::transformFirmware($firmware);
        }

        return (new DatatablesTransformer)->transformDatatables($array, $total);
    }

    public function transformFirmware(Firmware $firmware)
    {

        $default_field_values = array();

        $array = [
            'id' => (int) $firmware->id,
            'name' => e($firmware->name),
            'manufacturer' => ($firmware->manufacturer) ? [
                'id' => (int) $firmware->manufacturer->id,
                'name'=> e($firmware->manufacturer->name),
            ] : null,
            'major_release' => e($firmware->major_release),
            'minor_release' => e($firmware->minor_release),
            'assets_count' => (int) $firmware->assets_count,
            'category' => ($firmware->category) ? [
                'id' => (int) $firmware->category->id,
                'name'=> e($firmware->category->name),
            ] : null,
            'eol' => ($firmware->eol > 0) ? $firmware->eol.' months' : 'None',
            'eos' => ($firmware->eol > 0) ? $firmware->eol.' months' : 'None',
            'notes' => Helper::parseEscapedMarkedownInline($firmware->notes),
            'created_at' => Helper::getFormattedDateObject($firmware->created_at, 'datetime'),
            'updated_at' => Helper::getFormattedDateObject($firmware->updated_at, 'datetime'),
            'deleted_at' => Helper::getFormattedDateObject($firmware->deleted_at, 'datetime'),

        ];

        $permissions_array['available_actions'] = [
            'update' => (Gate::allows('update', Firmware::class) && ($firmware->deleted_at == '')),
            'delete' => $firmware->isDeletable(),
            'clone' => (Gate::allows('create', Firmware::class) && ($firmware->deleted_at == '')),
            'restore' => (Gate::allows('create', Firmware::class) && ($firmware->deleted_at != '')),
        ];

        $array += $permissions_array;

        return $array;
    }

    public function transformFirmwareDatatable($firmware_array)
    {
        return (new DatatablesTransformer)->transformDatatables($firmware_array);
    }
}
