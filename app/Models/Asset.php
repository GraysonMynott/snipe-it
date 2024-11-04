<?php

namespace App\Models;

use App\Events\AssetCheckedOut;
use App\Events\CheckoutableCheckedOut;
use App\Exceptions\CheckoutNotAllowed;
use App\Helpers\Helper;
use App\Http\Traits\UniqueUndeletedTrait;
use App\Models\Traits\Searchable;
use App\Presenters\Presentable;
use App\Presenters\AssetPresenter;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;
use Watson\Validating\ValidatingTrait;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

/**
 * Model for Assets.
 *
 * @version    v1.0
 */
class Asset extends SnipeModel
{

    protected $presenter = AssetPresenter::class;

    use CompanyableTrait;
    use HasFactory, Loggable, Requestable, Presentable, SoftDeletes, ValidatingTrait, UniqueUndeletedTrait;

    public const LOCATION = 'location';
    public const ASSET = 'asset';
    public const USER = 'user';

    /**
    * The database table used by the model.
    *
    * @var string
    */
    protected $table = 'assets';

    /**
    * Whether the model should inject it's identifier to the unique
    * validation rules before attempting validation. If this property
    * is not set in the model it will default to true.
    *
     * @var bool
    */
    protected $injectUniqueIdentifier = true;

    protected $casts = [
        'purchase_date'     => 'date',
        'eol_explicit'      => 'boolean',
        'last_patch_date'   => 'datetime',
        'next_patch_date'   => 'datetime:m-d-Y',
        'model_id'          => 'integer',
        'status_id'         => 'integer',
        'company_id'        => 'integer',
        'location_id'       => 'integer',
        'rtd_company_id'    => 'integer',
        'created_at'        => 'datetime',
        'updated_at'        => 'datetime',
        'deleted_at'        => 'datetime',
    ];

    protected $rules = [
        'model_id'          => 'required|integer|exists:models,id,deleted_at,NULL|not_array',
        'status_id'         => 'required|integer|exists:status_labels,id',
        'asset_tag'         => 'required|min:1|max:255|unique_undeleted:assets,asset_tag|not_array',
        'name'              => 'nullable|max:255',
        'company_id'        => 'nullable|integer|exists:companies,id',
        'last_patch_date'   => 'nullable|date_format:Y-m-d H:i:s',
        //'mac_address'       => 'required|max:18',
        'mac_address'       => 'nullable|regex:/^([0-9A-Fa-f]{2}[:-]){5}([0-9A-Fa-f]{2})$/',
        'next_patch_date'   => 'nullable|date',
        'location_id'       => 'nullable|exists:locations,id',
        'rtd_location_id'   => 'nullable|exists:locations,id',
        'purchase_date'     => 'nullable|date|date_format:Y-m-d',
        'serial'            => 'nullable|unique_undeleted:assets,serial',
        'asset_eol_date'    => 'nullable|date',
        'eol_explicit'      => 'nullable|boolean',
        'notes'             => 'nullable|string|max:65535',
    ];


  /**
   * The attributes that are mass assignable.
   *
   * @var array
   */
    protected $fillable = [
        'asset_tag',
        'assigned_to',
        'assigned_type',
        'company_id',
        'image',
        'location_id',
        'mac_address',
        'model_id',
        'name',
        'notes',
        'rtd_location_id',
        'serial',
        'status_id',
        'asset_eol_date',
        'eol_explicit',
        'last_patch_date',
        'next_patch_date',
        'asset_eol_date',
    ];

    use Searchable;

    /**
     * The attributes that should be included when searching the model.
     *
     * @var array
     */
    protected $searchableAttributes = [
      'name',
      'asset_tag',
      'serial',
      'mac_address',
      'notes',
      'created_at',
      'updated_at',
      'next_patch_date',
      'last_patch_date',
      'asset_eol_date',
    ];

    /**
     * The relations and their attributes that should be included when searching the model.
     *
     * @var array
     */
    protected $searchableRelations = [
        'assetstatus'        => ['name'],
        'company'            => ['name'],
        'defaultLoc'         => ['name'],
        'location'           => ['name'],
        'model'              => ['name', 'model_number', 'eol'],
        'model.category'     => ['name'],
        'model.manufacturer' => ['name'],
    ];

    /**
     * This handles the custom field validation for assets
     *
     * @var array
     */
    public function save(array $params = [])
    {
        if ($this->model_id != '') {
            $model = AssetModel::find($this->model_id);

            if (($model) && ($model->fieldset)) {

                foreach ($model->fieldset->fields as $field){
                    if($field->format == 'BOOLEAN'){
                        $this->{$field->db_column} = filter_var($this->{$field->db_column}, FILTER_VALIDATE_BOOLEAN);
                    }
                }

                $this->rules += $model->fieldset->validation_rules();

                if ($this->model->fieldset){
                    foreach ($this->model->fieldset->fields as $field){
                        if($field->format == 'BOOLEAN'){
                            $this->{$field->db_column} = filter_var($this->{$field->db_column}, FILTER_VALIDATE_BOOLEAN);
                        }
                    }
                }
            }
        }

        return parent::save($params);
    }


    public function getDisplayNameAttribute()
    {
        return $this->present()->name();
    }

    /**
     * Establishes the asset -> company relationship
     *
     * @author [A. Gianotto] [<snipe@snipe.net>]
     * @since [v3.0]
     * @return \Illuminate\Database\Eloquent\Relations\Relation
     */
    public function company()
    {
        return $this->belongsTo(\App\Models\Company::class, 'company_id');
    }

    /**
     * Pulls in the validation rules
     *
     * @author [A. Gianotto] [<snipe@snipe.net>]
     * @since [v3.0]
     * @return array
     */
    public function validationRules()
    {
        return $this->rules;
    }


    /**
     * Get uploads for this asset
     *
     * @author [A. Gianotto] [<snipe@snipe.net>]
     * @since [v4.0]
     * @return \Illuminate\Database\Eloquent\Relations\Relation
     */
    public function uploads()
    {
        return $this->hasMany('\App\Models\Actionlog', 'item_id')
                  ->where('item_type', '=', Asset::class)
                  ->where('action_type', '=', 'uploaded')
                  ->whereNotNull('filename')
                  ->orderBy('created_at', 'desc');
    }

    /*
    public function checkedOutToLocation(): bool
    {
      return $this->assignedType() === self::LOCATION;
    }
    */

    /**
     * Get the asset's location based on the assigned user
     *
     * @todo Refactor this if possible. It's awful.
     *
     * @author [A. Gianotto] [<snipe@snipe.net>]
     * @since [v4.0]
     * @return \ArrayObject
     */
    public function assetLoc($iterations = 1, $first_asset = null)
    {
        // if (! empty($this->assignedType())) {
        //     if ($this->assignedType() == self::ASSET) {
        //         if (! $first_asset) {
        //             $first_asset = $this;
        //         }
        //         if ($iterations > 10) {
        //             throw new \Exception('Asset assignment Loop for Asset ID: '.$first_asset->id);
        //         }
        //     }
        //     if ($this->assignedType() == self::USER) {
        //         return $this->defaultLoc;
        //     }
        // }
        return $this->defaultLoc;
    }

    /**
     * Gets the lowercased name of the type of target the asset is assigned to
     *
     * @author [A. Gianotto] [<snipe@snipe.net>]
     * @since [v4.0]
     * @return string
     */
    public function assignedType()
    {
        return $this->assigned_type ? strtolower(class_basename($this->assigned_type)) : null;
    }

    /**
     * This is annoying, but because we don't say "assets" in our route names, we have to make an exception here
     * @todo - normalize the route names - API endpoint URLS can stay the same
     *
     * @author [A. Gianotto] [<snipe@snipe.net>]
     * @since [v6.1.0]
     * @return string
     
    public function targetShowRoute()
    {
        $route = str_plural($this->assignedType());
        if ($route=='assets') {
            return 'hardware';
        }

        return $route;

    }
    */

    /**
     * Get the asset's location based on default RTD location
     *
     * @author [A. Gianotto] [<snipe@snipe.net>]
     * @since [v2.0]
     * @return \Illuminate\Database\Eloquent\Relations\Relation
     */
    public function defaultLoc()
    {
        return $this->belongsTo(\App\Models\Location::class, 'rtd_location_id');
    }

    /**
     * Get the image URL of the asset.
     *
     * Check first to see if there is a specific image uploaded to the asset,
     * and if not, check for an image uploaded to the asset model.
     *
     * @author [A. Gianotto] [<snipe@snipe.net>]
     * @since [v2.0]
     * @return string | false
     */
    public function getImageUrl()
    {
        if ($this->image && ! empty($this->image)) {
            return Storage::disk('public')->url(app('assets_upload_path').e($this->image));
        } elseif ($this->model && ! empty($this->model->image)) {
            return Storage::disk('public')->url(app('models_upload_path').e($this->model->image));
        }

        return false;
    }

    /**
     * Get the asset's logs
     *
     * @author [A. Gianotto] [<snipe@snipe.net>]
     * @since [v2.0]
     * @return \Illuminate\Database\Eloquent\Relations\Relation
     */
    public function assetlog()
    {
        return $this->hasMany(\App\Models\Actionlog::class, 'item_id')
                  ->where('item_type', '=', self::class)
                  ->orderBy('created_at', 'desc')
                  ->withTrashed();
    }

    /**
     * Get action logs history for this asset
     *
     * @author [A. Gianotto] [<snipe@snipe.net>]
     * @since [v1.0]
     * @return \Illuminate\Database\Eloquent\Relations\Relation
     */
    public function adminuser()
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id');
    }

    /**
     * Establishes the asset -> status relationship
     *
     * @author [A. Gianotto] [<snipe@snipe.net>]
     * @since [v1.0]
     * @return \Illuminate\Database\Eloquent\Relations\Relation
     */
    public function assetstatus()
    {
        return $this->belongsTo(\App\Models\Statuslabel::class, 'status_id');
    }

    /**
     * Establishes the asset -> model relationship
     *
     * @author [A. Gianotto] [<snipe@snipe.net>]
     * @since [v1.0]
     * @return \Illuminate\Database\Eloquent\Relations\Relation
     */
    public function model()
    {
        return $this->belongsTo(\App\Models\AssetModel::class, 'model_id')->withTrashed();
    }

    /**
     * Establishes the asset -> assigned licenses relationship
     *
     * @author [A. Gianotto] [<snipe@snipe.net>]
     * @since [v4.0]
     * @return \Illuminate\Database\Eloquent\Relations\Relation
     */
    public function licenses()
    {
        return $this->belongsToMany(\App\Models\License::class, 'license_seats', 'asset_id', 'license_id');
    }

    /**
     * Establishes the asset -> license seats relationship
     *
     * @author [A. Gianotto] [<snipe@snipe.net>]
     * @since [v4.0]
     * @return \Illuminate\Database\Eloquent\Relations\Relation
     */
    public function licenseseats()
    {
        return $this->hasMany(\App\Models\LicenseSeat::class, 'asset_id');
    }

    /**
     * Establishes the asset -> location relationship
     *
     * @author [A. Gianotto] [<snipe@snipe.net>]
     * @since [v2.0]
     * @return \Illuminate\Database\Eloquent\Relations\Relation
     */
    public function location()
    {
        return $this->belongsTo(\App\Models\Location::class, 'location_id');
    }

    /**
     * Get the next autoincremented asset tag
     *
     * @author [A. Gianotto] [<snipe@snipe.net>]
     * @since [v4.0]
     * @return string | false
     */
    public static function autoincrement_asset(int $additional_increment = 0)
    {
        $settings = \App\Models\Setting::getSettings();


        if ($settings->auto_increment_assets == '1') {
            if ($settings->zerofill_count > 0) {
                return $settings->auto_increment_prefix.self::zerofill($settings->next_auto_tag_base + $additional_increment, $settings->zerofill_count);
            }

            return $settings->auto_increment_prefix.($settings->next_auto_tag_base + $additional_increment);
        } else {
            return false;
        }
    }

    /**
     * Get the next base number for the auto-incrementer.
     *
     * We'll add the zerofill and prefixes on the fly as we generate the number.
     *
     * @author [A. Gianotto] [<snipe@snipe.net>]
     * @since [v4.0]
     * @return int
     */
    public static function nextAutoIncrement($assets)
    {

        $max = 1;

        foreach ($assets as $asset) {
            $results = preg_match("/\d+$/", $asset['asset_tag'], $matches);

            if ($results)
            {
                $number = $matches[0];

                if ($number > $max)
                {
                    $max = $number;
                }
            }
        }


    }

    /**
     * Add zerofilling based on Settings
     *
     * We'll add the zerofill and prefixes on the fly as we generate the number.
     *
     * @author [A. Gianotto] [<snipe@snipe.net>]
     * @since [v4.0]
     * @return string
     */
    public static function zerofill($num, $zerofill = 3)
    {
        return str_pad($num, $zerofill, '0', STR_PAD_LEFT);
    }

    /**
     * Determine whether this asset's next patch date is before the last patch date
     *
     * @return bool
     * @since [v6.4.1]
     * @author [A. Gianotto] [<snipe@snipe.net>]
     * */
    public function checkInvalidNextPatchDate()
    {
        if (($this->last_patch_date) && ($this->next_patch_date) && ($this->last_patch_date > $this->next_patch_date)) {
            return true;
        }
        return false;
    }

    /**
     * -----------------------------------------------
     * BEGIN MUTATORS
     * -----------------------------------------------
     **/

    /**
     * Make sure the next_patch_date is formatted as Y-m-d.
     *
     * This is kind of dumb and confusing, since we already cast it that way AND it's a date field
     * in the database, but here we are.
     *
     * @param $value
     * @return void
     */

    protected function nextPatchDate(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $value ? Carbon::parse($value)->format('Y-m-d') : null,
            set: fn ($value) => $value ? Carbon::parse($value)->format('Y-m-d') : null,
        );
    }

    protected function lastPatchDate(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $value ? Carbon::parse($value)->format('Y-m-d H:i:s') : null,
            set: fn ($value) => $value ? Carbon::parse($value)->format('Y-m-d H:i:s') : null,
        );
    }

    protected function assetEolDate(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $value ? Carbon::parse($value)->format('Y-m-d') : null,
            set: fn ($value) => $value ? Carbon::parse($value)->format('Y-m-d') : null,
        );
    }


    /**
    * -----------------------------------------------
    * BEGIN QUERY SCOPES
    * -----------------------------------------------
    **/

    /**
     * Run additional, advanced searches.
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param  array  $terms The search terms
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function advancedTextSearch(Builder $query, array $terms)
    {

        /**
         * Assigned user
         */
        $query = $query->leftJoin('users as assets_users', function ($leftJoin) {
            $leftJoin->on('assets_users.id', '=', 'assets.assigned_to')
                ->where('assets.assigned_type', '=', User::class);
        });

        foreach ($terms as $term) {

            $query = $query
                ->orWhere('assets_users.first_name', 'LIKE', '%'.$term.'%')
                ->orWhere('assets_users.last_name', 'LIKE', '%'.$term.'%')
                ->orWhere('assets_users.username', 'LIKE', '%'.$term.'%')
                ->orWhere('assets_users.employee_num', 'LIKE', '%'.$term.'%')
                ->orWhereMultipleColumns([
                    'assets_users.first_name',
                    'assets_users.last_name',
                ], $term);
        }

        /**
         * Assigned location
         */
        $query = $query->leftJoin('locations as assets_locations', function ($leftJoin) {
            $leftJoin->on('assets_locations.id', '=', 'assets.assigned_to')
                ->where('assets.assigned_type', '=', Location::class);
        });

        foreach ($terms as $term) {

            $query = $query->orWhere('assets_locations.name', 'LIKE', '%'.$term.'%');
        }

        /**
         * Assigned assets
         */
        $query = $query->leftJoin('assets as assigned_assets', function ($leftJoin) {
            $leftJoin->on('assigned_assets.id', '=', 'assets.assigned_to')
                ->where('assets.assigned_type', '=', self::class);
        });

        foreach ($terms as $term) {
            $query = $query->orWhere('assigned_assets.name', 'LIKE', '%'.$term.'%');

        }

        return $query;
    }


    /**
    * Query builder scope for hardware
    *
    * @param  \Illuminate\Database\Query\Builder $query Query builder instance
    *
    * @return \Illuminate\Database\Query\Builder          Modified query builder
    */

    public function scopeHardware($query)
    {
        return $query->where('physical', '=', '1');
    }

    /**
    * Query builder scope for pending assets
    *
    * @param  \Illuminate\Database\Query\Builder $query Query builder instance
    *
    * @return \Illuminate\Database\Query\Builder          Modified query builder
    */

    public function scopePending($query)
    {
        return $query->whereHas('assetstatus', function ($query) {
            $query->where('deployable', '=', 0)
                ->where('pending', '=', 1)
                ->where('archived', '=', 0);
        });
    }


    /**
    * Query builder scope for searching location
    *
    * @param  \Illuminate\Database\Query\Builder $query Query builder instance
    *
    * @return \Illuminate\Database\Query\Builder          Modified query builder
    */

    public function scopeAssetsByLocation($query, $location)
    {
        return $query->where(function ($query) use ($location) {
            $query->whereHas('assignedTo', function ($query) use ($location) {
                $query->where([
                    ['users.location_id', '=', $location->id],
                    ['assets.assigned_type', '=', User::class],
                ])->orWhere([
                    ['locations.id', '=', $location->id],
                    ['assets.assigned_type', '=', Location::class],
                ])->orWhere([
                    ['assets.rtd_location_id', '=', $location->id],
                    ['assets.assigned_type', '=', self::class],
                ]);
            })->orWhere(function ($query) use ($location) {
                $query->where('assets.rtd_location_id', '=', $location->id);
                $query->whereNull('assets.assigned_to');
            });
        });
    }


    /**
    * Query builder scope for RTD assets
    *
    * @param  \Illuminate\Database\Query\Builder $query Query builder instance
    *
    * @return \Illuminate\Database\Query\Builder          Modified query builder
    */

    public function scopeRTD($query)
    {
        return $query->whereNull('assets.assigned_to')
                   ->whereHas('assetstatus', function ($query) {
                       $query->where('deployable', '=', 1)
                             ->where('pending', '=', 0)
                             ->where('archived', '=', 0);
                   });
    }

  /**
   * Query builder scope for Undeployable assets
   *
   * @param  \Illuminate\Database\Query\Builder $query Query builder instance
   *
   * @return \Illuminate\Database\Query\Builder          Modified query builder
   */

    public function scopeUndeployable($query)
    {
        return $query->whereHas('assetstatus', function ($query) {
            $query->where('deployable', '=', 0)
                ->where('pending', '=', 0)
                ->where('archived', '=', 0);
        });
    }

    /**
     * Query builder scope for non-Archived assets
     *
     * @param  \Illuminate\Database\Query\Builder $query Query builder instance
     *
     * @return \Illuminate\Database\Query\Builder          Modified query builder
     */

    public function scopeNotArchived($query)
    {
        return $query->whereHas('assetstatus', function ($query) {
            $query->where('archived', '=', 0);
        });
    }

    /**
     * Query builder scope for Assets that are due for patching, based on the assets.next_patch_date
     * and settings.patch_warning_days.
     *
     * This is/will be used in the artisan command snipeit:upcoming-patches and also
     * for an upcoming API call for retrieving a report on assets that will need to be patched.
     *
     * Due for patch soon:
     * next_patch_date greater than or equal to now (must be in the future)
     * and (next_patch_date - threshold days) <= now ()
     *
     * Example:
     * next_patch_date = May 4, 2025
     * threshold for alerts = 30 days
     * now = May 4, 2019
     *
     * @author A. Gianotto <snipe@snipe.net>
     * @since v4.6.16
     * @param Setting $settings
     *
     * @return \Illuminate\Database\Query\Builder          Modified query builder
     */

    public function scopeDueForPatch($query, $settings)
    {
        $interval = $settings->patch_warning_days ?? 0;
        $today = Carbon::now();
        $interval_date = $today->copy()->addDays($interval)->format('Y-m-d');

        return $query->whereNotNull('assets.next_patch_date')
            ->whereBetween('assets.next_patch_date', [$today->format('Y-m-d'), $interval_date])
            ->where('assets.archived', '=', 0)
            ->NotArchived();
    }

    /**
     * Query builder scope for Assets that are OVERDUE for patching, based on the assets.next_patch_date
     * and settings.patch_warning_days. It checks to see if assets.next patch_date is before now
     *
     * This is/will be used in the artisan command snipeit:upcoming-patches and also
     * for an upcoming API call for retrieving a report on overdue assets.
     *
     * @author A. Gianotto <snipe@snipe.net>
     * @since v4.6.16
     * @param Setting $settings
     *
     * @return \Illuminate\Database\Query\Builder          Modified query builder
     */

    public function scopeOverdueForPatch($query)
    {
        return $query->whereNotNull('assets.next_patch_date')
            ->where('assets.next_patch_date', '<', Carbon::now()->format('Y-m-d'))
            ->where('assets.archived', '=', 0)
            ->NotArchived();
    }

    /**
     * Query builder scope for Assets that are due for patching OR overdue, based on the assets.next_patch_date
     * and settings.patch_warning_days.
     *
     * This is/will be used in the artisan command snipeit:upcoming-patches and also
     * for an upcoming API call for retrieving a report on assets that will need to be patched.
     *
     * @author A. Gianotto <snipe@snipe.net>
     * @since v4.6.16
     * @param Setting $settings
     *
     * @return \Illuminate\Database\Query\Builder          Modified query builder
     */

    public function scopeDueOrOverdueForPatch($query, $settings)
    {

        return $query->where(function ($query) {
            $query->OverdueForPatch();
        })->orWhere(function ($query) use ($settings) {
            $query->DueForPatch($settings);
        });
    }


    /**
     * Query builder scope for Archived assets counting
     *
     * This is primarily used for the tab counters so that IF the admin
     * has chosen to not display archived assets in their regular lists
     * and views, it will return the correct number.
     *
     * @param  \Illuminate\Database\Query\Builder $query Query builder instance
     *
     * @return \Illuminate\Database\Query\Builder          Modified query builder
     */
    public function scopeAssetsForShow($query)
    {

        if (Setting::getSettings()->show_archived_in_list!=1) {
            return $query->whereHas('assetstatus', function ($query) {
                $query->where('archived', '=', 0);
            });
        } else {
            return $query;
        }

    }

    /**
     * Query builder scope for Archived assets
     *
     * @param  \Illuminate\Database\Query\Builder $query Query builder instance
     *
     * @return \Illuminate\Database\Query\Builder          Modified query builder
     */

    public function scopeArchived($query)
    {
        return $query->whereHas('assetstatus', function ($query) {
            $query->where('deployable', '=', 0)
                ->where('pending', '=', 0)
                ->where('archived', '=', 1);
        });
    }

    /**
     * Query builder scope for Deployed assets
     *
     * @param  \Illuminate\Database\Query\Builder $query Query builder instance
     *
     * @return \Illuminate\Database\Query\Builder          Modified query builder
     */
    public function scopeDeployed($query)
    {
        return $query->where('assigned_to', '>', '0');
    }

    /**
     * scopeInModelList
     * Get all assets in the provided listing of model ids
     *
     * @param       $query
     * @param array $modelIdListing
     *
     * @return mixed
     * @author  Vincent Sposato <vincent.sposato@gmail.com>
     * @version v1.0
     */
    public function scopeInModelList($query, array $modelIdListing)
    {
        return $query->whereIn('assets.model_id', $modelIdListing);
    }

    /**
     * Query builder scope to search on text for complex Bootstrap Tables API.
     *
     * @param  \Illuminate\Database\Query\Builder  $query  Query builder instance
     * @param  text                              $search      Search term
     *
     * @return \Illuminate\Database\Query\Builder          Modified query builder
     */
    public function scopeAssignedSearch($query, $search)
    {
        $search = explode(' OR ', $search);

        return $query->leftJoin('users as assets_users', function ($leftJoin) {
            $leftJoin->on('assets_users.id', '=', 'assets.assigned_to')
                ->where('assets.assigned_type', '=', User::class);
        })->leftJoin('locations as assets_locations', function ($leftJoin) {
            $leftJoin->on('assets_locations.id', '=', 'assets.assigned_to')
                ->where('assets.assigned_type', '=', Location::class);
        })->leftJoin('assets as assigned_assets', function ($leftJoin) {
            $leftJoin->on('assigned_assets.id', '=', 'assets.assigned_to')
                ->where('assets.assigned_type', '=', self::class);
        })->where(function ($query) use ($search) {
            foreach ($search as $search) {
                $query->whereHas('model', function ($query) use ($search) {
                    $query->whereHas('category', function ($query) use ($search) {
                        $query->where(function ($query) use ($search) {
                            $query->where('categories.name', 'LIKE', '%'.$search.'%')
                                ->orWhere('models.name', 'LIKE', '%'.$search.'%')
                                ->orWhere('models.model_number', 'LIKE', '%'.$search.'%');
                        });
                    });
                })->orWhereHas('model', function ($query) use ($search) {
                    $query->whereHas('manufacturer', function ($query) use ($search) {
                        $query->where(function ($query) use ($search) {
                            $query->where('manufacturers.name', 'LIKE', '%'.$search.'%');
                        });
                    });
                })->orWhere(function ($query) use ($search) {
                    $query->where('assets_users.first_name', 'LIKE', '%'.$search.'%')
                        ->orWhere('assets_users.last_name', 'LIKE', '%'.$search.'%')
                        ->orWhereMultipleColumns([
                            'assets_users.first_name',
                            'assets_users.last_name',
                        ], $search)
                        ->orWhere('assets_users.username', 'LIKE', '%'.$search.'%')
                        ->orWhere('assets_locations.name', 'LIKE', '%'.$search.'%')
                        ->orWhere('assigned_assets.name', 'LIKE', '%'.$search.'%');
                })->orWhere('assets.name', 'LIKE', '%'.$search.'%')
                    ->orWhere('assets.asset_tag', 'LIKE', '%'.$search.'%')
                    ->orWhere('assets.serial', 'LIKE', '%'.$search.'%')
                    ->orWhere('assets.notes', 'LIKE', '%'.$search.'%');
            }

        })->withTrashed()->whereNull('assets.deleted_at'); //workaround for laravel bug
    }

    /**
     * Query builder scope to search the department ID of users assigned to assets
     *
     * @author [A. Gianotto] [<snipe@snipe.net>]
     * @since [v5.0]
     * @return string | false
     *
     * @return \Illuminate\Database\Query\Builder Modified query builder
     */
    public function scopeCheckedOutToTargetInDepartment($query, $search)
    {
        return $query->leftJoin('users as assets_dept_users', function ($leftJoin) {
            $leftJoin->on('assets_dept_users.id', '=', 'assets.assigned_to')
                ->where('assets.assigned_type', '=', User::class);
        })->where(function ($query) use ($search) {
                    $query->where('assets_dept_users.department_id', '=', $search);

        })->withTrashed()->whereNull('assets.deleted_at'); //workaround for laravel bug
    }



    /**
     * Query builder scope to search on text filters for complex Bootstrap Tables API
     *
     * @param  \Illuminate\Database\Query\Builder  $query  Query builder instance
     * @param  text   $filter   JSON array of search keys and terms
     *
     * @return \Illuminate\Database\Query\Builder          Modified query builder
     */
    public function scopeByFilter($query, $filter)
    {
        return $query->where(function ($query) use ($filter) {
            foreach ($filter as $key => $search_val) {

                $fieldname = str_replace('custom_fields.', '', $key);

                if ($fieldname == 'asset_tag') {
                    $query->where('assets.asset_tag', 'LIKE', '%'.$search_val.'%');
                }

                if ($fieldname == 'name') {
                    $query->where('assets.name', 'LIKE', '%'.$search_val.'%');
                }


                if ($fieldname =='serial') {
                    $query->where('assets.serial', 'LIKE', '%'.$search_val.'%');
                }

                if ($fieldname == 'notes') {
                    $query->where('assets.notes', 'LIKE', '%'.$search_val.'%');
                }

                if ($fieldname == 'status_label') {
                    $query->whereHas('assetstatus', function ($query) use ($search_val) {
                        $query->where('status_labels.name', 'LIKE', '%'.$search_val.'%');
                    });
                }

                if ($fieldname == 'location') {
                    $query->whereHas('location', function ($query) use ($search_val) {
                        $query->where('locations.name', 'LIKE', '%'.$search_val.'%');
                    });
                }

                if ($fieldname == 'rtd_location') {
                    $query->whereHas('defaultLoc', function ($query) use ($search_val) {
                        $query->where('locations.name', 'LIKE', '%'.$search_val.'%');
                    });
                }

                if ($fieldname =='assigned_to') {
                    $query->whereHasMorph('assignedTo', [User::class], function ($query) use ($search_val) {
                        $query->where(function ($query) use ($search_val) {
                            $query->where('users.first_name', 'LIKE', '%'.$search_val.'%')
                                ->orWhere('users.last_name', 'LIKE', '%'.$search_val.'%');
                        });
                    });
                }


                if ($fieldname == 'manufacturer') {
                    $query->whereHas('model', function ($query) use ($search_val) {
                        $query->whereHas('manufacturer', function ($query) use ($search_val) {
                            $query->where(function ($query) use ($search_val) {
                                $query->where('manufacturers.name', 'LIKE', '%'.$search_val.'%');
                            });
                        });
                    });
                }

                if ($fieldname == 'category') {
                    $query->whereHas('model', function ($query) use ($search_val) {
                        $query->whereHas('category', function ($query) use ($search_val) {
                            $query->where(function ($query) use ($search_val) {
                                $query->where('categories.name', 'LIKE', '%'.$search_val.'%')
                                    ->orWhere('models.name', 'LIKE', '%'.$search_val.'%')
                                    ->orWhere('models.model_number', 'LIKE', '%'.$search_val.'%');
                            });
                        });
                    });
                }

                if ($fieldname == 'model') {
                    $query->where(function ($query) use ($search_val) {
                        $query->whereHas('model', function ($query) use ($search_val) {
                            $query->where('models.name', 'LIKE', '%'.$search_val.'%');
                        });
                    });
                }

                if ($fieldname == 'model_number') {
                    $query->where(function ($query) use ($search_val) {
                        $query->whereHas('model', function ($query) use ($search_val) {
                            $query->where('models.model_number', 'LIKE', '%'.$search_val.'%');
                        });
                    });
                }


                if ($fieldname == 'company') {
                    $query->where(function ($query) use ($search_val) {
                        $query->whereHas('company', function ($query) use ($search_val) {
                            $query->where('companies.name', 'LIKE', '%'.$search_val.'%');
                        });
                    });
                }
            

            /**
             * THIS CLUNKY BIT IS VERY IMPORTANT
             *
             * Although inelegant, this section matters a lot when querying against fields that do not
             * exist on the asset table. There's probably a better way to do this moving forward, for
             * example using the Schema:: methods to determine whether or not a column actually exists,
             * or even just using the $searchableRelations variable earlier in this file.
             *
             * In short, this set of statements tells the query builder to ONLY query against an
             * actual field that's being passed if it doesn't meet known relational fields. This
             * allows us to query custom fields directly in the assets table
             * (regardless of their name) and *skip* any fields that we already know can only be
             * searched through relational searches that we do earlier in this method.
             *
             * For example, we do not store "location" as a field on the assets table, we store
             * that relationship through location_id on the assets table, therefore querying
             * assets.location would fail, as that field doesn't exist -- plus we're already searching
             * against those relationships earlier in this method.
             *
             * - snipe 
             *
             */

            if (($fieldname!='category') && ($fieldname!='model_number') && ($fieldname!='rtd_location') && ($fieldname!='location')
                && ($fieldname!='status_label') && ($fieldname!='assigned_to') && ($fieldname!='model') && ($fieldname!='company') && ($fieldname!='manufacturer')) {
                    $query->where('assets.'.$fieldname, 'LIKE', '%' . $search_val . '%');
            }


            }


        });

    }


    /**
    * Query builder scope to order on model
    *
    * @param  \Illuminate\Database\Query\Builder  $query  Query builder instance
    * @param  text                              $order       Order
    *
    * @return \Illuminate\Database\Query\Builder          Modified query builder
    */
    public function scopeOrderModels($query, $order)
    {
        return $query->join('models as asset_models', 'assets.model_id', '=', 'asset_models.id')->orderBy('asset_models.name', $order);
    }

    /**
    * Query builder scope to order on model number
    *
    * @param  \Illuminate\Database\Query\Builder  $query  Query builder instance
    * @param  text                              $order       Order
    *
    * @return \Illuminate\Database\Query\Builder          Modified query builder
    */
    public function scopeOrderModelNumber($query, $order)
    {
        return $query->leftJoin('models as model_number_sort', 'assets.model_id', '=', 'model_number_sort.id')->orderBy('model_number_sort.model_number', $order);
    }


    /**
    * Query builder scope to order on assigned user
    *
    * @param  \Illuminate\Database\Query\Builder  $query  Query builder instance
    * @param  text                              $order       Order
    *
    * @return \Illuminate\Database\Query\Builder          Modified query builder
    */
    public function scopeOrderAssigned($query, $order)
    {
        return $query->leftJoin('users as users_sort', 'assets.assigned_to', '=', 'users_sort.id')->select('assets.*')->orderBy('users_sort.first_name', $order)->orderBy('users_sort.last_name', $order);
    }

    /**
    * Query builder scope to order on status
    *
    * @param  \Illuminate\Database\Query\Builder  $query  Query builder instance
    * @param  text                              $order       Order
    *
    * @return \Illuminate\Database\Query\Builder          Modified query builder
    */
    public function scopeOrderStatus($query, $order)
    {
        return $query->join('status_labels as status_sort', 'assets.status_id', '=', 'status_sort.id')->orderBy('status_sort.name', $order);
    }

    /**
    * Query builder scope to order on company
    *
    * @param  \Illuminate\Database\Query\Builder  $query  Query builder instance
    * @param  text                              $order       Order
    *
    * @return \Illuminate\Database\Query\Builder          Modified query builder
    */
    public function scopeOrderCompany($query, $order)
    {
        return $query->leftJoin('companies as company_sort', 'assets.company_id', '=', 'company_sort.id')->orderBy('company_sort.name', $order);
    }


    /**
     * Query builder scope to return results of a category
     *
     * @param  \Illuminate\Database\Query\Builder  $query  Query builder instance
     * @param  text $order Order
     *
     * @return \Illuminate\Database\Query\Builder          Modified query builder
     */
    public function scopeInCategory($query, $category_id)
    {
        return $query->join('models as category_models', 'assets.model_id', '=', 'category_models.id')
            ->join('categories', 'category_models.category_id', '=', 'categories.id')->where('category_models.category_id', '=', $category_id);
    }

    /**
     * Query builder scope to return results of a manufacturer
     *
     * @param  \Illuminate\Database\Query\Builder  $query  Query builder instance
     * @param  text $order Order
     *
     * @return \Illuminate\Database\Query\Builder          Modified query builder
     */
    public function scopeByManufacturer($query, $manufacturer_id)
    {
        return $query->join('models', 'assets.model_id', '=', 'models.id')
            ->join('manufacturers', 'models.manufacturer_id', '=', 'manufacturers.id')->where('models.manufacturer_id', '=', $manufacturer_id);
    }



    /**
    * Query builder scope to order on category
    *
    * @param  \Illuminate\Database\Query\Builder  $query  Query builder instance
    * @param  text                              $order         Order
    *
    * @return \Illuminate\Database\Query\Builder          Modified query builder
    */
    public function scopeOrderCategory($query, $order)
    {
        return $query->join('models as order_model_category', 'assets.model_id', '=', 'order_model_category.id')
            ->join('categories as category_order', 'order_model_category.category_id', '=', 'category_order.id')
            ->orderBy('category_order.name', $order);
    }


    /**
     * Query builder scope to order on manufacturer
     *
     * @param  \Illuminate\Database\Query\Builder  $query  Query builder instance
     * @param  text                              $order         Order
     *
     * @return \Illuminate\Database\Query\Builder          Modified query builder
     */
    public function scopeOrderManufacturer($query, $order)
    {
        return $query->join('models as order_asset_model', 'assets.model_id', '=', 'order_asset_model.id')
            ->leftjoin('manufacturers as manufacturer_order', 'order_asset_model.manufacturer_id', '=', 'manufacturer_order.id')
            ->orderBy('manufacturer_order.name', $order);
    }

   /**
    * Query builder scope to order on location
    *
    * @param  \Illuminate\Database\Query\Builder  $query  Query builder instance
    * @param  text                              $order       Order
    *
    * @return \Illuminate\Database\Query\Builder          Modified query builder
    */
    public function scopeOrderLocation($query, $order)
    {
        return $query->leftJoin('locations as asset_locations', 'asset_locations.id', '=', 'assets.location_id')->orderBy('asset_locations.name', $order);
    }

    /**
     * Query builder scope to order on default
     * @param  \Illuminate\Database\Query\Builder  $query  Query builder instance
     * @param  text                              $order       Order
     *
     * @return \Illuminate\Database\Query\Builder          Modified query builder
     */
    public function scopeOrderRtdLocation($query, $order)
    {
        return $query->leftJoin('locations as rtd_asset_locations', 'rtd_asset_locations.id', '=', 'assets.rtd_location_id')->orderBy('rtd_asset_locations.name', $order);
    }

    /**
     * Query builder scope to search on location ID
     *
     * @param  \Illuminate\Database\Query\Builder  $query  Query builder instance
     * @param  text                              $search      Search term
     *
     * @return \Illuminate\Database\Query\Builder          Modified query builder
     */
    public function scopeByLocationId($query, $search)
    {
        return $query->where(function ($query) use ($search) {
            $query->whereHas('location', function ($query) use ($search) {
                $query->where('locations.id', '=', $search);
            });
        });

    }


}
