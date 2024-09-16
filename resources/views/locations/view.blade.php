@extends('layouts/default')

{{-- Page title --}}
@section('title')

 {{ trans('general.location') }}:
 {{ $location->name }}
 
@parent
@stop

{{-- Page content --}}
@section('content')

<div class="row">
  <div class="col-md-9">

      <div class="nav-tabs-custom">
          <ul class="nav nav-tabs hidden-print">

              <li class="active">
                  <a href="#assets" data-toggle="tab">
                    <span class="hidden-lg hidden-md">
                        <i class="fas fa-barcode fa-2x" aria-hidden="true"></i>
                    </span>
                    <span class="hidden-xs hidden-sm">
                          {{ trans('admin/locations/message.current_location') }}
                          {!! ($location->assets()->AssetsForShow()->count() > 0 ) ? '<badge class="badge badge-secondary">'.number_format($location->assets()->AssetsForShow()->count()).'</badge>' : '' !!}
                    </span>
                  </a>
              </li>

              <li>
                  <a href="#assets_assigned" data-toggle="tab">
                    <span class="hidden-lg hidden-md">
                        <i class="fas fa-barcode fa-2x" aria-hidden="true"></i>
                    </span>
                      <span class="hidden-xs hidden-sm">
                          {{ trans('admin/locations/message.assigned_assets') }}
                          {!! ($location->assignedAssets()->AssetsForShow()->count() > 0 ) ? '<badge class="badge badge-secondary">'.number_format($location->assignedAssets()->AssetsForShow()->count()).'</badge>' : '' !!}
                    </span>
                  </a>
              </li>
              
              <li>
                  <a href="#history" data-toggle="tab">
                    <span class="hidden-lg hidden-md">
                        <i class="fas fa-hdd fa-2x" aria-hidden="true"></i>
                    </span>
                      <span class="hidden-xs hidden-sm">
                          {{ trans('general.history') }}
                    </span>
                  </a>
              </li>
          </ul>


          <div class="tab-content">
              <div class="tab-pane active" id="users">
                    <h2 class="box-title">{{ trans('general.users') }}</h2>
                      <div class="table table-responsive">
                          @include('partials.users-bulk-actions')
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
                                  data-toolbar="#userBulkEditToolbar"
                                  data-bulk-button-id="#bulkUserEditButton"
                                  data-bulk-form-id="#usersBulkForm"
                                  data-click-to-select="true"
                                  id="usersTable"
                                  class="table table-striped snipe-table"
                                  data-url="{{route('api.users.index', ['location_id' => $location->id])}}"
                                  data-export-options='{
                              "fileName": "export-locations-{{ str_slug($location->name) }}-users-{{ date('Y-m-d') }}",
                              "ignoreColumn": ["actions","image","change","checkbox","checkincheckout","icon"]
                              }'>

                          </table>
                      </div><!-- /.table-responsive -->
              </div><!-- /.tab-pane -->

              <div class="tab-pane" id="assets">
                      <h2 class="box-title">{{ trans('admin/locations/message.current_location') }}</h2>

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
                                  data-url="{{route('api.assets.index', ['location_id' => $location->id]) }}"
                                  data-export-options='{
                              "fileName": "export-locations-{{ str_slug($location->name) }}-assets-{{ date('Y-m-d') }}",
                              "ignoreColumn": ["actions","image","change","checkbox","checkincheckout","icon"]
                              }'>
                          </table>

                      </div><!-- /.table-responsive -->
              </div><!-- /.tab-pane -->

              <div class="tab-pane" id="assets_assigned">
                  <h2 class="box-title">
                      {{ trans('admin/locations/message.assigned_assets') }}
                  </h2>

                  <div class="table table-responsive">
                      @include('partials.asset-bulk-actions', ['id_divname' => 'AssignedAssetsBulkEditToolbar', 'id_formname' => 'assignedAssetsBulkForm', 'id_button' => 'AssignedbulkAssetEditButton'])
                      <table
                              data-columns="{{ \App\Presenters\AssetPresenter::dataTableLayout() }}"
                              data-cookie-id-table="assetsAssignedListingTable"
                              data-pagination="true"
                              data-id-table="assetsAssignedListingTable"
                              data-search="true"
                              data-side-pagination="server"
                              data-show-columns="true"
                              data-show-export="true"
                              data-show-refresh="true"
                              data-sort-order="asc"
                              data-toolbar="#AssignedAssetsBulkEditToolbar"
                              data-bulk-button-id="#AssignedbulkAssetEditButton"
                              data-bulk-form-id="#assignedAssetsBulkForm"
                              data-click-to-select="true"
                              id="assetsListingTable"
                              class="table table-striped snipe-table"
                              data-url="{{route('api.assets.index', ['assigned_to' => $location->id, 'assigned_type' => \App\Models\Location::class]) }}"
                              data-export-options='{
                              "fileName": "export-locations-{{ str_slug($location->name) }}-assets-{{ date('Y-m-d') }}",
                              "ignoreColumn": ["actions","image","change","checkbox","checkincheckout","icon"]
                              }'>
                      </table>

                  </div><!-- /.table-responsive -->
              </div><!-- /.tab-pane -->

                <div class="tab-pane" id="history">
                    <h2 class="box-title">{{ trans('general.history') }}</h2>
                    <!-- checked out assets table -->
                    <div class="row">
                        <div class="col-md-12">
                            <table
                                    class="table table-striped snipe-table"
                                    id="assetHistory"
                                    data-pagination="true"
                                    data-id-table="assetHistory"
                                    data-search="true"
                                    data-side-pagination="server"
                                    data-show-columns="true"
                                    data-show-fullscreen="true"
                                    data-show-refresh="true"
                                    data-sort-order="desc"
                                    data-sort-name="created_at"
                                    data-show-export="true"
                                    data-export-options='{
                        "fileName": "export-location-asset-{{  $location->id }}-history",
                        "ignoreColumn": ["actions","image","change","checkbox","checkincheckout","icon"]
                    }'

                    data-url="{{ route('api.activity.index', ['target_id' => $location->id, 'target_type' => 'location']) }}"
                    data-cookie-id-table="assetHistory"
                    data-cookie="true">
                                <thead>
                                    <tr>
                                        <th data-visible="true" data-field="icon" style="width: 40px;" class="hidden-xs" data-formatter="iconFormatter">{{ trans('admin/assets/table.icon') }}</th>
                                        <th class="col-sm-2" data-visible="true" data-field="action_date" data-formatter="dateDisplayFormatter">{{ trans('general.date') }}</th>
                                        <th class="col-sm-1" data-visible="true" data-field="admin" data-formatter="usersLinkObjFormatter">{{ trans('general.admin') }}</th>
                                        <th class="col-sm-1" data-visible="true" data-field="action_type">{{ trans('general.action') }}</th>
                                        <th class="col-sm-2" data-visible="true" data-field="item" data-formatter="polymorphicItemFormatter">{{ trans('general.item') }}</th>
                                        <th class="col-sm-2" data-visible="true" data-field="target" data-formatter="polymorphicItemFormatter">{{ trans('general.target') }}</th>
                                        <th class="col-sm-2" data-field="note">{{ trans('general.notes') }}</th>
                                        <th class="col-md-3" data-field="signature_file" data-visible="false"  data-formatter="imageFormatter">{{ trans('general.signature') }}</th>
                                        <th class="col-md-3" data-visible="false" data-field="file" data-visible="false"  data-formatter="fileUploadFormatter">{{ trans('general.download') }}</th>
                                        <th class="col-sm-2" data-field="log_meta" data-visible="true" data-formatter="changeLogFormatter">{{ trans('admin/assets/table.changed')}}</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div> <!-- /.row -->
                </div> <!-- /.tab-pane history -->
          </div><!--/.col-md-9-->
      </div><!--/.col-md-9-->
  </div><!--/.col-md-9-->

  <div class="col-md-3">

      <div class="col-md-12">
          <a href="{{ route('locations.edit', ['location' => $location->id]) }}" style="width: 100%;" class="btn btn-sm btn-primary pull-left">{{ trans('admin/locations/table.update') }} </a>
      </div>

      <div class="col-md-12">
        <ul class="list-unstyled" style="line-height: 25px; padding-bottom: 20px;">
          @if ($location->address!='')
            <li>{{ $location->address }}</li>
           @endif
            @if ($location->address2!='')
              <li>{{ $location->address2 }}</li>
            @endif
            @if (($location->city!='') || ($location->state!='') || ($location->zip!=''))
              <li>{{ $location->city }} {{ $location->state }} {{ $location->zip }}</li>
            @endif
        </ul>

        @if (($location->state!='') && ($location->country!='') && (config('services.google.maps_api_key')))
          <div class="col-md-12 text-center">
            <img src="https://maps.googleapis.com/maps/api/staticmap?markers={{ urlencode($location->address.','.$location->city.' '.$location->state.' '.$location->country.' '.$location->zip) }}&size=700x500&maptype=roadmap&key={{ config('services.google.maps_api_key') }}" class="img-thumbnail" style="width:100%" alt="Map">
          </div>
        @endif
      </div>
  </div>
  </div>
</div>

@stop

@section('moar_scripts')
@include ('partials.bootstrap-table', [
    'exportFile' => 'locations-export',
    'search' => true
 ])

@stop
