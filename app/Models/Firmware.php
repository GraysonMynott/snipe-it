<?php

namespace App\Models;

use App\Models\Traits\Searchable;
use App\Presenters\Presentable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Watson\Validating\ValidatingTrait;
use \App\Presenters\FirmwarePresenter;

/**
 * Model for Asset Models. Asset Models contain higher level
 * attributes that are common among the same type of asset.
 *
 * @version    v1.0
 */
class Firmware extends SnipeModel
{
    use HasFactory;
    use SoftDeletes;
    protected $presenter = \App\Presenters\FirmwarePresenter::class;
    use Loggable, Presentable;

    protected $table = 'firmware';
    protected $hidden = ['user_id', 'deleted_at'];

    // Declare the rules for the model validation
    protected $rules = [
        'name'              => 'required|min:1|max:255',
        'major_release'     => 'required|max:255',
        'minor_release'     => 'max:255|nullable',
        'eol'               => 'integer:min:0|max:240|nullable',
        'eos'               => 'integer:min:0|max:240|nullable',
        'category_id'       => 'required|integer|exists:categories,id',
        'manufacturer_id'   => 'integer|exists:manufacturers,id|nullable',
    ];

    /**
     * Whether the model should inject its identifier to the unique
     * validation rules before attempting validation. If this property
     * is not set in the model it will default to true.
     *
     * @var bool
     */
    protected $injectUniqueIdentifier = true;
    use ValidatingTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'major_release',
        'minor_release',
        'recommended',
        'eol',
        'eos',
        'notes',
        'user_id',
        'manufacturer_id',
        'category_id',
    ];

    use Searchable;

    /**
     * The attributes that should be included when searching the model.
     *
     * @var array
     */
    protected $searchableAttributes = ['name', 'major_release', 'notes', 'eol'];

    /**
     * The relations and their attributes that should be included when searching the model.
     *
     * @var array
     */
    protected $searchableRelations = [
        'category'     => ['name'],
        'manufacturer' => ['name'],
    ];

    /**
     * Establishes the model -> assets relationship
     *
     * @author [A. Gianotto] [<snipe@snipe.net>]
     * @since [v1.0]
     * @return \Illuminate\Database\Eloquent\Relations\Relation
     * TODO: Rename to getAssets()
     */
    public function getAssets()
    {
        return $this->hasMany(\App\Models\Asset::class, 'firmware_id');
    }

    /**
     * Establishes the model -> category relationship
     *
     * @author [A. Gianotto] [<snipe@snipe.net>]
     * @since [v1.0]
     * @return \Illuminate\Database\Eloquent\Relations\Relation
     * TODO: Rename to getCategory()
     */
    public function getCategory()
    {
        return $this->belongsTo(\App\Models\Category::class, 'category_id');
    }

    /**
     * Establishes the model -> manufacturer relationship
     *
     * @author [A. Gianotto] [<snipe@snipe.net>]
     * @since [v1.0]
     * @return \Illuminate\Database\Eloquent\Relations\Relation
     */
    public function getManufacturer()
    {
        return $this->belongsTo(\App\Models\Manufacturer::class, 'manufacturer_id');
    }

    /**
     * Establishes the model -> custom field default values relationship
     *
     * @author hannah tinkler
     * @since [v4.3]
     * @return \Illuminate\Database\Eloquent\Relations\Relation
     */
    public function defaultValues()
    {
        return $this->belongsToMany(\App\Models\CustomField::class, 'models_custom_fields')->withPivot('default_value');
    }


    /**
     * Checks if the model is deletable
     *
     * @author A. Gianotto <snipe@snipe.net>
     * @since [v6.3.4]
     * @return bool
     */
    public function isDeletable()
    {
        return Gate::allows('delete', $this)
            && ($this->assets_count == 0)
            && ($this->deleted_at == '');
    }


    /**
     * -----------------------------------------------
     * BEGIN QUERY SCOPES
     * -----------------------------------------------
     **/

    /**
     * scopeInCategory
     * Get all models that are in the array of category ids
     *
     * @param       $query
     * @param array $categoryIdListing
     *
     * @return mixed
     * @author  Vincent Sposato <vincent.sposato@gmail.com>
     * @version v1.0
     */
    public function scopeInCategory($query, array $categoryIdListing)
    {
        return $query->whereIn('category_id', $categoryIdListing);
    }

    /**
     * Query builder scope to search on text, including catgeory and manufacturer name
     *
     * @param  Illuminate\Database\Query\Builder  $query  Query builder instance
     * @param  text                              $search      Search term
     *
     * @return Illuminate\Database\Query\Builder          Modified query builder
     */
    public function scopeSearchByManufacturerOrCat($query, $search)
    {
        return $query->where('models.name', 'LIKE', "%$search%")
            ->orWhere('model_number', 'LIKE', "%$search%")
            ->orWhere(function ($query) use ($search) {
                $query->whereHas('category', function ($query) use ($search) {
                    $query->where('categories.name', 'LIKE', '%'.$search.'%');
                });
            })
            ->orWhere(function ($query) use ($search) {
                $query->whereHas('manufacturer', function ($query) use ($search) {
                    $query->where('manufacturers.name', 'LIKE', '%'.$search.'%');
                });
            });
    }

    /**
     * Query builder scope to order on manufacturer
     *
     * @param  \Illuminate\Database\Query\Builder  $query  Query builder instance
     * @param  text                              $order       Order
     *
     * @return \Illuminate\Database\Query\Builder          Modified query builder
     */
    public function scopeOrderManufacturer($query, $order)
    {
        return $query->leftJoin('manufacturers', 'firmware.manufacturer_id', '=', 'manufacturers.id')->orderBy('manufacturers.name', $order);
    }

    /**
     * Query builder scope to order on category name
     *
     * @param  \Illuminate\Database\Query\Builder  $query  Query builder instance
     * @param  text                              $order       Order
     *
     * @return \Illuminate\Database\Query\Builder          Modified query builder
     */
    public function scopeOrderCategory($query, $order)
    {
        return $query->leftJoin('categories', 'firmware.category_id', '=', 'categories.id')->orderBy('categories.name', $order);
    }
}
