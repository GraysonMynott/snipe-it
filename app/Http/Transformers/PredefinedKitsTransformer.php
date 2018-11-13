<?php
namespace App\Http\Transformers;

use App\Models\PredefinedKit;
use Illuminate\Database\Eloquent\Collection;
use Gate;
use App\Helpers\Helper;
use Illuminate\Support\Facades\Storage;
use App\Models\SnipeModel;

class PredefinedKitsTransformer
{

    public function transformPredefinedKits (Collection $kits, $total)
    {
        $array = array();
        foreach ($kits as $kit) {
            $array[] = self::transformPredefinedKit($kit);
        }
        return (new DatatablesTransformer)->transformDatatables($array, $total);
    }

    public function transformPredefinedKit (PredefinedKit $kit)
    {
        $array = [
            'id' => (int) $kit->id,
            'name' => e($kit->name)
        ];

        $permissions_array['available_actions'] = [
            'update' => Gate::allows('update', PredefinedKit::class),
            'delete' => Gate::allows('delete', PredefinedKit::class),
            'checkout' => Gate::allows('checkout', PredefinedKit::class) ? true : false,
            // 'clone' => Gate::allows('create', PredefinedKit::class),
            // 'restore' => Gate::allows('create', PredefinedKit::class),
        ];
        $array['user_can_checkout'] = true;
        $array += $permissions_array;
        return $array;
    }

    public function transformElements(Collection $elements, $total) {
        $array = array();
        foreach ($elements as $element) {
            $array[] = self::transformElement($element);
        }
        return (new DatatablesTransformer)->transformDatatables($array, $total);
    }

    public function transformElement(SnipeModel $element) {
        $array = [
            'id' => (int) $element->id,
            'pivot_id' => (int) $element->pivot->id,
            'owner_id' => (int) $element->pivot->kit_id,
            'quantity' => (int) $element->pivot->quantity,
            'name' => e($element->name)
        ];

        $permissions_array['available_actions'] = [
            'update' => Gate::allows('update', PredefinedKit::class),
            'delete' => Gate::allows('delete', PredefinedKit::class),
        ];

        $array += $permissions_array;
        return $array;
    }

    public function transformPredefinedKitsDatatable($kits) {
        return (new DatatablesTransformer)->transformDatatables($kits);
    }


}
