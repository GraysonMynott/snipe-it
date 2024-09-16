<?php

namespace App\Models;

use App\Http\Traits\UniqueUndeletedTrait;
use App\Models\Asset;
use App\Models\SnipeModel;
use App\Models\Traits\Searchable;
use App\Models\User;
use App\Presenters\Presentable;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Gate;
use Watson\Validating\ValidatingTrait;

class Location extends SnipeModel
{
    use HasFactory;

    protected $presenter = \App\Presenters\LocationPresenter::class;
    use Presentable;
    use SoftDeletes;

    protected $table = 'locations';
    protected $rules = [
        'name'          => 'required|min:2|max:255|unique_undeleted',
        'address'       => 'max:191|nullable',
        'address2'      => 'max:191|nullable',
        'city'          => 'max:191|nullable',
        'state'         => 'min:2|max:191|nullable',
        'country'       => 'min:2|max:191|nullable',
        'zip'           => 'max:10|nullable',
    ];

    protected $casts = [];

    /**
     * Whether the model should inject it's identifier to the unique
     * validation rules before attempting validation. If this property
     * is not set in the model it will default to true.
     *
     * @var bool
     */
    protected $injectUniqueIdentifier = true;
    use ValidatingTrait;
    use UniqueUndeletedTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'address',
        'address2',
        'city',
        'state',
        'country',
        'zip',
        'phone',
        'image',
    ];
    protected $hidden = ['user_id'];

    use Searchable;

    /**
     * The attributes that should be included when searching the model.
     *
     * @var array
     */
    protected $searchableAttributes = ['name', 'address', 'city', 'state', 'zip', 'created_at', 'phone'];

    /**
     * The relations and their attributes that should be included when searching the model.
     *
     * @var array
     */
    protected $searchableRelations = [];


    /**
     * Determine whether or not this location can be deleted.
     *
     * This method requires the eager loading of the relationships in order to determine whether
     * it can be deleted. It's tempting to load those here, but that increases the query load considerably.
     *
     * @author A. Gianotto <snipe@snipe.net>
     * @since [v3.0]
     * @return bool
     */
    public function isDeletable()
    {

        return Gate::allows('delete', $this)
                && ($this->assets_count === 0);
    }

    /**
     * Find assets with this location as their location_id
     *
     * @author A. Gianotto <snipe@snipe.net>
     * @since [v3.0]
     * @return \Illuminate\Database\Eloquent\Relations\Relation
     */
    public function assets()
    {
        return $this->hasMany(\App\Models\Asset::class, 'location_id')
            ->whereHas('assetstatus', function ($query) {
                $query->where('status_labels.deployable', '=', 1)
                        ->orWhere('status_labels.pending', '=', 1)
                        ->orWhere('status_labels.archived', '=', 0);
            });
    }
}
