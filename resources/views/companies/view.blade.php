@extends('layouts/default')

{{-- Page title --}}
@section('title')
    {{ $company->name }}
    @parent
@stop

{{-- Page content --}}
@section('content')

    <div class="row">
        <div class="col-md-12">
            <div class="nav-tabs-custom">
                <ul class="nav nav-tabs">


                    <li class="active">
                        <a href="#asset_tab" data-toggle="tab">
                            <span class="hidden-lg hidden-md">
                            <i class="fas fa-barcode" aria-hidden="true"></i>
                            </span>
                            <span class="hidden-xs hidden-sm">{{ trans('general.assets') }}
                                {!! ($company->assets()->AssetsForShow()->count() > 0 ) ? '<badge class="badge badge-secondary">'.number_format($company->assets()->AssetsForShow()->count()).'</badge>' : '' !!}

                            </span>
                        </a>
                    </li>

                    <li>
                        <a href="#licenses_tab" data-toggle="tab">
                            <span class="hidden-lg hidden-md">
                            <i class="far fa-save"></i>
                            </span>
                            <span class="hidden-xs hidden-sm">{{ trans('general.licenses') }}
                                {!! ($company->licenses->count() > 0 ) ? '<badge class="badge badge-secondary">'.number_format($company->licenses->count()).'</badge>' : '' !!}
                            </span>
                        </a>
                    </li>

                     <li>
                        <a href="#users_tab" data-toggle="tab">
                            <span class="hidden-lg hidden-md">
                            <i class="fas fa-users"></i></span>
                            <span class="hidden-xs hidden-sm">{{ trans('general.people') }}
                                {!! (($company->users) && ($company->users->count() > 0 )) ? '<badge class="badge badge-secondary">'.number_format($company->users->count()).'</badge>' : '' !!}
                            </span>
                        </a>
                    </li>



                </ul>

                <div class="tab-content">

                    <div class="tab-pane fade in active" id="asset_tab">
                        <!-- checked out assets table -->
                        <div class="table table-responsive">
                            @include('partials.asset-bulk-actions')

                            <table
                                    data-columns="{{ \App\Presenters\AssetPresenter::dataTableLayout() }}"
                                    data-cookie-id-table="assetsListingTable"
                                    data-pagination="true"
                                    data-id-table="assetsListingTable"
                                    data-search="true"
                                    data-side-pagination="server"
                                    data-show-columns="true"
                                    data-show-export="true"
                                    data-show-refresh="true"
                                    data-sort-order="asc"
                                    data-toolbar="#assetsBulkEditToolbar"
                                    data-bulk-button-id="#bulkAssetEditButton"
                                    data-bulk-form-id="#assetsBulkForm"
                                    data-click-to-select="true"
                                    id="assetsListingTable"
                                    class="table table-striped snipe-table"
                                    data-url="{{route('api.assets.index',['company_id' => $company->id]) }}"
                                    data-export-options='{
                              "fileName": "export-companies-{{ str_slug($company->name) }}-assets-{{ date('Y-m-d') }}",
                              "ignoreColumn": ["actions","image","change","checkbox","checkincheckout","icon"]
                              }'>
                            </table>
                        </div>
                    </div><!-- /asset_tab -->

                    <div class="tab-pane" id="licenses_tab">
                        <div class="table-responsive">

                            <table
                                    data-columns="{{ \App\Presenters\LicensePresenter::dataTableLayout() }}"
                                    data-cookie-id-table="licensesTable"
                                    data-pagination="true"
                                    data-id-table="licensesTable"
                                    data-search="true"
                                    data-side-pagination="server"
                                    data-show-columns="true"
                                    data-show-export="true"
                                    data-show-refresh="true"
                                    data-sort-order="asc"
                                    id="licensesTable"
                                    class="table table-striped snipe-table"
                                    data-url="{{route('api.licenses.index',['company_id' => $company->id]) }}"
                                    data-export-options='{
                              "fileName": "export-companies-{{ str_slug($company->name) }}-licenses-{{ date('Y-m-d') }}",
                              "ignoreColumn": ["actions","image","change","checkbox","checkincheckout","icon"]
                              }'>
                            </table>

                        </div>
                    </div><!-- /licenses-tab -->

                    <div class="tab-pane" id="users_tab">
                        <div class="table-responsive">

                            <table
                                    data-columns="{{ \App\Presenters\UserPresenter::dataTableLayout() }}"
                                    data-cookie-id-table="usersTable"
                                    data-pagination="true"
                                    data-id-table="usersTable"
                                    data-search="true"
                                    data-side-pagination="server"
                                    data-show-columns="true"
                                    data-show-export="true"
                                    data-show-refresh="true"
                                    data-sort-order="asc"
                                    id="usersTable"
                                    class="table table-striped snipe-table"
                                    data-url="{{route('api.users.index',['company_id' => $company->id]) }}"
                                    data-export-options='{
                              "fileName": "export-companies-{{ str_slug($company->name) }}-users-{{ date('Y-m-d') }}",
                              "ignoreColumn": ["actions","image","change","checkbox","checkincheckout","icon"]
                              }'>

                            </table>
                        </div>
                    </div><!-- /consumables-tab -->




                </div><!-- /.tab-content -->
            </div><!-- nav-tabs-custom -->
        </div>
    </div>

@stop
@section('moar_scripts')
    @include ('partials.bootstrap-table')

@stop

