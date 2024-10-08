<?php

namespace App\Http\Transformers;

use App\Helpers\Helper;
use App\Models\Category;
use Illuminate\Support\Facades\Gate;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Storage;

class CategoriesTransformer
{
    public function transformCategories(Collection $categorys, $total)
    {
        $array = [];
        foreach ($categorys as $category) {
            $array[] = self::transformCategory($category);
        }

        return (new DatatablesTransformer)->transformDatatables($array, $total);
    }

    public function transformCategory(Category $category = null)
    {

        // We only ever use item_count for categories in this transformer, so it makes sense to keep it
        // simple and do this switch here.
        switch ($category->category_type) {
            case 'asset':
                $category->item_count = $category->assets_count;
                break;
            case 'license':
                $category->item_count = $category->licenses_count;
                break;
            default:
                $category->item_count = 0;
        }

        if ($category) {
            $array = [
                'id' => (int) $category->id,
                'name' => e($category->name),
                'image' =>   ($category->image) ? Storage::disk('public')->url('categories/'.e($category->image)) : null,
                'category_type' => Helper::categoryTypeList($category->category_type),
                'item_count' => (int) $category->item_count,
                'assets_count' => (int) $category->assets_count,
                'licenses_count' => (int) $category->licenses_count,
                'created_at' => Helper::getFormattedDateObject($category->created_at, 'datetime'),
                'updated_at' => Helper::getFormattedDateObject($category->updated_at, 'datetime'),
            ];

            $permissions_array['available_actions'] = [
                'update' => Gate::allows('update', Category::class),
                'delete' => $category->isDeletable(),
            ];

            $array += $permissions_array;

            return $array;
        }
    }
}
