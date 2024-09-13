<?php

use App\Http\Controllers\Api;
// use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group(['prefix' => 'v1', 'middleware' => ['api', 'throttle:api']], function () {

    /**
     * Base 404 Route
     */
    Route::get('/', function () {
        return response()->json(
            [
                'status' => 'error',
                'message' => '404 endpoint not found. This is the base URL for the API and does not return anything itself. Please check the API reference at https://snipe-it.readme.io/reference to find a valid API endpoint.',
                'payload' => null,
            ], 404);
    });

    /**
     * Account routes
     */
    Route::group(['prefix' => 'account'], function () {

        Route::get('requests',
            [
                Api\ProfileController::class, 
                'requestedAssets'
            ]
        )->name('api.assets.requested');

        Route::get('requestable/hardware',
            [
                Api\AssetsController::class, 
                'requestable'
            ]
        )->name('api.assets.requestable');

        Route::post('personal-access-tokens',
            [
                Api\ProfileController::class,
                'createApiToken'
            ]
        )->name('api.personal-access-token.create');

        Route::get('personal-access-tokens',
            [
                Api\ProfileController::class,
                'showApiTokens'
            ]
        )->name('api.personal-access-token.index');

        Route::delete('personal-access-tokens/{tokenId}',
            [
                Api\ProfileController::class,
                'deleteApiToken'
            ]
        )->name('api.personal-access-token.delete');
    }); // end account group

    /**
     * Categories API routes
     */
    Route::group(['prefix' => 'categories'], function () {   
        Route::get('{item_type}/selectlist',
            [
                Api\CategoriesController::class, 
                'selectlist'
            ]
        )->name('api.categories.selectlist');
    });

    Route::resource('categories', 
        Api\CategoriesController::class,
        ['names' => [
                'index' => 'api.categories.index',
                'show' => 'api.categories.show',
                'update' => 'api.categories.update',
                'store' => 'api.categories.store',
                'destroy' => 'api.categories.destroy',
            ],
        'except' => ['create', 'edit'],
        'parameters' => ['category' => 'category_id'],
        ]
    ); // end category API routes

    /**
     * Companies API routes
     */
    Route::group(['prefix' => 'companies'], function () {    
        Route::get('selectlist',
            [
                Api\CompaniesController::class, 
                'selectlist'
            ]
        )->name('api.companies.selectlist');

    }); 

    Route::resource('companies', 
        Api\CompaniesController::class,
        ['names' => [
                'index' => 'api.companies.index',
                'show' => 'api.companies.show',
                'update' => 'api.companies.update',
                'store' => 'api.companies.store',
                'destroy' => 'api.companies.destroy',
            ],
        'except' => ['create', 'edit'],
        'parameters' => ['company' => 'company_id'],
        ]
    ); // end companies API routes

    /**
     * Fields API routes
     */
    Route::group(['prefix' => 'fields'], function () {
        Route::post('fieldsets/{id}/order',
            [
                Api\CustomFieldsController::class, 
                'postReorder'
            ]
        )->name('api.customfields.order');

        Route::post('{field}/associate',
            [
                Api\CustomFieldsController::class, 
                'associate'
            ]
        )->name('api.customfields.associate');

        Route::post('{field}/disassociate',
            [
                Api\CustomFieldsController::class, 
                'disassociate'
            ]
        )->name('api.customfields.disassociate');
    });

    Route::resource('fields', 
    Api\CustomFieldsController::class,
        ['names' => 
            [
                'index' => 'api.customfields.index',
                'show' => 'api.customfields.show',
                'update' => 'api.customfields.update',
                'store' => 'api.customfields.store',
                'destroy' => 'api.customfields.destroy',
            ],
        'except' => ['create', 'edit'],
        'parameters' => ['field' => 'field_id'],
        ]
    ); // end custom fields API routes

    /**
     * Fieldsets API routes
     */
    Route::group(['prefix' => 'fieldsets'], function () {
    
        Route::post('{fieldset}/fields',
            [
                Api\CustomFieldsetsController::class, 
                'fields'
            ]
        )->name('api.fieldsets.fields');

        Route::post('{fieldset}/fields/{model}',
            [
                Api\CustomFieldsetsController::class, 
                'fieldsWithDefaultValues'
            ]
        )->name('api.fieldsets.fields-with-default-value');

    });

    Route::resource('fieldsets', 
    Api\CustomFieldsetsController::class,
        ['names' => [
                'index' => 'api.fieldsets.index',
                'show' => 'api.fieldsets.show',
                'update' => 'api.fieldsets.update',
                'store' => 'api.fieldsets.store',
                'destroy' => 'api.fieldsets.destroy',
            ],
        'except' => ['create', 'edit'],
        'parameters' => ['fieldset' => 'fieldset_id'],
        ]
    ); // end custom fieldsets API routes


    /**
     * Groups API routes
     */
    Route::resource('groups', 
    Api\GroupsController::class,
        ['names' => [
                'index' => 'api.groups.index',
                'show' => 'api.groups.show',
                'update' => 'api.groups.update',
                'store' => 'api.groups.store',
                'destroy' => 'api.groups.destroy',
            ],
        'except' => ['create', 'edit'],
        'parameters' => ['group' => 'group_id'],
        ]
    ); // end groups API routes
        

    /**
     * Assets API routes
     */
    Route::group(['prefix' => 'hardware'], function () {
    
        Route::get('selectlist',
            [
                Api\AssetsController::class, 
                'selectlist'
            ]
        )->name('assets.selectlist');

        Route::get('{asset_id}/licenses',
            [
                Api\AssetsController::class, 
                'licenses'
            ]
        )->name('api.assets.licenselist');

        Route::get('bytag/{tag}',
            [
                Api\AssetsController::class, 
                'showByTag'
            ]
        )->name('assets.show.bytag');

        Route::get('bytag/{any}',
            [
                Api\AssetsController::class, 
                'showByTag'
            ]
        )->name('api.assets.show.bytag')
        ->where('any', '.*');

        Route::get('byserial/{any}',
            [
                Api\AssetsController::class, 
                'showBySerial'
            ]
        )->name('api.assets.show.byserial')
        ->where('any', '.*');

        // LEGACY URL - Get assets that are due or overdue for patch
        Route::get('patch/{status}',
            [
                Api\AssetsController::class, 
                'index'
            ]
        )->name('api.asset.to-patch');

        // This gets the "due or overdue" API endpoints for patches and checkins
        Route::get('{action}/{upcoming_status}',
                [
                    Api\AssetsController::class,
                    'index'
                ]
        )->name('api.assets.list-upcoming')
        ->where(['action' => 'patches|checkins', 'upcoming_status' => 'due|overdue|due-or-overdue']);

        Route::post('patch',
            [
                Api\AssetsController::class, 
                'patch'
            ]
        )->name('api.asset.patch');

        Route::post('{id}/checkin',
            [
                Api\AssetsController::class, 
                'checkin'
            ]
        )->name('api.asset.checkin');

        Route::post('{id}/checkout',
            [
                Api\AssetsController::class, 
                'checkout'
            ]
        )->name('api.asset.checkout');

        Route::post('{asset_id}/restore',
            [
                Api\AssetsController::class,
                'restore'
            ]
        )->name('api.assets.restore');
        Route::post('{asset_id}/files',
            [
                Api\AssetFilesController::class,
                'store'
            ]
        )->name('api.assets.files');

        Route::get('{asset_id}/files',
            [
                Api\AssetFilesController::class,
                'list'
            ]
        )->name('api.assets.files');

        Route::get('{asset_id}/file/{file_id}',
            [
                Api\AssetFilesController::class,
                'show'
            ]
        )->name('api.assets.file');

        Route::delete('{asset_id}/file/{file_id}',
            [
                Api\AssetFilesController::class,
                'destroy'
            ]
        )->name('api.assets.file');

    });

    Route::resource('hardware', 
        Api\AssetsController::class,
        ['names' => [
                'index' => 'api.assets.index',
                'show' => 'api.assets.show',
                'update' => 'api.assets.update',
                'store' => 'api.assets.store',
                'destroy' => 'api.assets.destroy',
            ],
        'except' => ['create', 'edit'],
        'parameters' => ['asset' => 'asset_id'],
        ]
    ); // end assets API routes


    /**
     * Imports API routes
    */
    Route::group(['prefix' => 'imports'], function () {
        Route::post('process/{import}',
            [
                Api\ImportController::class, 
                'process'
            ]
        )->name('api.imports.importFile');
    }); 

    Route::resource('imports', 
        Api\ImportController::class,
        ['names' => [
                'index' => 'api.imports.index',
                'show' => 'api.imports.show',
                'update' => 'api.imports.update',
                'store' => 'api.imports.store',
                'destroy' => 'api.imports.destroy',
            ],
        'except' => ['create', 'edit'],
        'parameters' => ['import' => 'import_id'],
        ]
    ); // end imports API routes


    /**
     * Labels API routes
     */
    Route::group(['prefix' => 'labels'], function() {
        Route::get('{name}', [ Api\LabelsController::class, 'show'])
            ->where('name', '.*')
            ->name('api.labels.show');
        Route::get('', [ Api\LabelsController::class, 'index'])
            ->name('api.labels.index');
    });

    /**
     * Licenses API routes
    */
    Route::group(['prefix' => 'licenses'], function () {

    Route::get('selectlist',
        [
            Api\LicensesController::class, 
            'selectlist'
        ]
    )->name('api.licenses.selectlist');

    }); 

    Route::resource('licenses', 
    Api\LicensesController::class,
    ['names' => [
            'index' => 'api.licenses.index',
            'show' => 'api.licenses.show',
            'update' => 'api.licenses.update',
            'store' => 'api.licenses.store',
            'destroy' => 'api.licenses.destroy',
        ],
    'except' => ['create', 'edit'],
    'parameters' => ['licenses' => 'license_id'],
    ]
    ); 


    Route::resource('licenses.seats', 
    Api\LicenseSeatsController::class,
    ['names' => [
            'index' => 'api.licenses.seats.index',
            'show' => 'api.licenses.seats.show',
            'update' => 'api.licenses.seats.update',
        ],
    'except' => ['create', 'edit', 'destroy', 'store'],
    'parameters' => ['licenseseat' => 'licenseseat_id'],
    ]
    ); // end license API routes


    /**
     * Locations API routes
    */
    Route::group(['prefix' => 'locations'], function () {

        Route::get('selectlist',
            [
                Api\LocationsController::class, 
                'selectlist'
            ]
        )->name('api.locations.selectlist');

        Route::get('{location}/users',
            [
                Api\LocationsController::class, 
                'getDataViewUsers'
            ]
        )->name('api.locations.viewusers');

        Route::get('{location}/assets',
        [
            Api\LocationsController::class, 
            'getDataViewAssets'
        ]
        )->name('api.locations.viewassets');

    }); 

    Route::resource('locations', 
    Api\LocationsController::class,
    ['names' => [
            'index' => 'api.locations.index',
            'show' => 'api.locations.show',
            'update' => 'api.locations.update',
            'store' => 'api.locations.store',
            'destroy' => 'api.locations.destroy',
        ],
    'except' => ['create', 'edit'],
    'parameters' => ['location' => 'location_id'],
    ]
    ); // end locations API routes


    /**
    * Manufacturers API routes
    */
    Route::group(['prefix' => 'manufacturers'], function () {

        Route::get('selectlist',
            [
                Api\ManufacturersController::class, 
                'selectlist'
            ]
        )->name('api.manufacturers.selectlist');

        Route::post('{id}/restore',
            [
                Api\ManufacturersController::class,
                'restore'
            ]
        )->name('api.manufacturers.restore');

    }); 

    Route::resource('manufacturers', 
    Api\ManufacturersController::class,
    ['names' => [
            'index' => 'api.manufacturers.index',
            'show' => 'api.manufacturers.show',
            'update' => 'api.manufacturers.update',
            'store' => 'api.manufacturers.store',
            'destroy' => 'api.manufacturers.destroy',
        ],
    'except' => ['create', 'edit'],
    'parameters' => ['manufacturer' => 'manufacturer_id'],
    ]
    ); // end  manufacturers API routes


    /**
    * Asset models API routes
    */
    Route::group(['prefix' => 'models'], function () {

        Route::get('selectlist',
            [
                Api\AssetModelsController::class, 
                'selectlist'
            ]
        )->name('api.models.selectlist');

        Route::get('assets',
            [
                Api\AssetModelsController::class, 
                'assets'
            ]
        )->name('api.models.assets');

        Route::post('{id}/restore',
            [
                Api\AssetModelsController::class,
                'restore'
            ]
        )->name('api.models.restore');

    }); 

    Route::resource('models', 
        Api\AssetModelsController::class,
        ['names' => [
            'index' => 'api.models.index',
            'show' => 'api.models.show',
            'update' => 'api.models.update',
            'store' => 'api.models.store',
            'destroy' => 'api.models.destroy',
        ],
        'except' => ['create', 'edit'],
        'parameters' => ['model' => 'model_id'],
        ]
    ); // end asset models API routes



    /**
    * Settings API routes
    */
    Route::group(['middleware'=> ['auth', 'authorize:superuser'], 'prefix' => 'settings'], function () {

        Route::get('ldaptest',
            [
                Api\SettingsController::class, 
                'ldaptest'
            ]
        )->name('api.settings.ldaptest');

        Route::post('purge_barcodes',
            [
                Api\SettingsController::class, 
                'purgeBarcodes'
            ]
        )->name('api.settings.purgebarcodes');

        Route::get('login-attempts',
            [
                Api\SettingsController::class, 
                'showLoginAttempts'
            ]
        )->name('api.settings.login_attempts');

        Route::post('ldaptestlogin',
            [
                Api\SettingsController::class, 
                'ldaptestlogin'
            ]
        )->name('api.settings.ldaptestlogin');

        Route::post('slacktest',
        [
            Api\SettingsController::class, 
            'slacktest'
        ]
        )->name('api.settings.slacktest');

        Route::post('mailtest',
        [
            Api\SettingsController::class, 
            'ajaxTestEmail'
        ]
        )->name('api.settings.mailtest');

        Route::get('backups',
            [
                Api\SettingsController::class,
                'listBackups'
            ]
        )->name('api.settings.backups.index');

        Route::get('backups/download/latest',
            [
                Api\SettingsController::class,
                'downloadLatestBackup'
            ]
        )->name('api.settings.backups.latest');

        Route::get('backups/download/{file}',
            [
                Api\SettingsController::class,
                'downloadBackup'
            ]
        )->name('api.settings.backups.download');

    }); 
    
    Route::resource('settings', 
    Api\SettingsController::class,
    ['names' => [
            'index' => 'api.settings.index',
            'show' => 'api.settings.show',
            'update' => 'api.settings.update',
            'store' => 'api.settings.store',
            'destroy' => 'api.settings.destroy',
        ],
    'except' => ['create', 'edit'],
    'parameters' => ['setting' => 'setting_id'],
    ]
    ); // end settings API


    /**
    * Status labels API routes
    */
    Route::group(['prefix' => 'statuslabels'], function () {

        Route::get('selectlist',
            [
                Api\StatuslabelsController::class, 
                'selectlist'
            ]
        )->name('api.statuslabels.selectlist');

        Route::get('assets/name',
            [
                Api\StatuslabelsController::class, 
                'getAssetCountByStatuslabel'
            ]
        )->name('api.statuslabels.assets.byname');

        Route::get('assets/type',
            [
                Api\StatuslabelsController::class,
                'getAssetCountByMetaStatus'
            ]
        )->name('api.statuslabels.assets.bytype');

        Route::get('{id}/assetlist',
            [
                Api\StatuslabelsController::class, 
                'assets'
            ]
        )->name('api.statuslabels.assets');

        Route::get('{statuslabel}/deployable',
            [
                Api\StatuslabelsController::class, 
                'checkIfDeployable'
            ]
        )->name('api.statuslabels.deployable');

        Route::get('selectlist',
            [
                Api\StatuslabelsController::class,
                'selectlist'
            ]
        )->name('api.statuslabels.selectlist');

    }); 

    Route::resource('statuslabels', 
    Api\StatuslabelsController::class,
    ['names' => [
            'index' => 'api.statuslabels.index',
            'show' => 'api.statuslabels.show',
            'update' => 'api.statuslabels.update',
            'store' => 'api.statuslabels.store',
            'destroy' => 'api.statuslabels.destroy',
        ],
    'except' => ['create', 'edit'],
    'parameters' => ['statuslabel' => 'statuslabel_id'],
    ]
    ); // end status labels API routes


    /**
    * Users API routes
    */
    Route::group(['prefix' => 'users'], function () {

        Route::get('selectlist',
            [
                Api\UsersController::class, 
                'selectlist'
            ]
        )->name('api.users.selectlist');

        Route::post('two_factor_reset',
            [
                Api\UsersController::class, 
                'postTwoFactorReset'
            ]
        )->name('api.users.two_factor_reset');

        Route::get('me',
            [
                Api\UsersController::class, 
                'getCurrentUserInfo'
            ]
        )->name('api.users.me');

        Route::get('list/{status?}',
        [
            Api\UsersController::class, 
            'getDatatable'
        ]
        )->name('api.users.list');

        Route::get('{user}/assets',
        [
            Api\UsersController::class, 
            'assets'
        ]
        )->name('api.users.assetlist');

        Route::post('{user}/email',
            [
                Api\UsersController::class,
                'emailAssetList'
            ]
        )->name('api.users.email_assets');

        Route::get('{user}/licenses',
        [
            Api\UsersController::class, 
            'licenses'
        ]
        )->name('api.users.licenselist');

        Route::post('{user}/upload',
        [
            Api\UsersController::class, 
            'postUpload'
        ]
        )->name('api.users.uploads');

        Route::post('{user}/restore',
            [
                Api\UsersController::class,
                'restore'
            ]
        )->name('api.users.restore');

    }); 

    Route::resource('users', 
    Api\UsersController::class,
    ['names' => [
            'index' => 'api.users.index',
            'show' => 'api.users.show',
            'update' => 'api.users.update',
            'store' => 'api.users.store',
            'destroy' => 'api.users.destroy',
        ],
    'except' => ['create', 'edit'],
    'parameters' => ['user' => 'user_id'],
    ]
    ); // end users API routes


    /**
     * Reports API routes
     */
    
    Route::group(['prefix' => 'reports'], function () {

        Route::get('activity',
        [
            Api\ReportsController::class, 
            'index'
        ]
        )->name('api.activity.index');
    }); // end reports api routes

    /**
     * Version API routes
     */

    Route::get('/version', function () {
        return response()->json(
            [
                'version' => config('version.app_version'),
            ], 200);
    }); // end version api routes


    Route::fallback(function () {
        return response()->json(
            [
                'status' => 'error',
                'message' => '404 endpoint not found. Please check the API reference at https://snipe-it.readme.io/reference to find a valid API endpoint.',
                'payload' => null,
            ], 404);
    }); // end fallback routes

}); // end API routes
