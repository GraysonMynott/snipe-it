@extends('layouts/default')

{{-- Page title --}}
@section('title')
    {{ trans('admin/assets/general.view') }} {{ $asset->asset_tag }}
    @parent
@stop

{{-- Page content --}}
@section('content')

    <div class="row">

        @if (!$asset->model)
            <div class="col-md-12">
                <div class="callout callout-danger">
                      <p><strong>{{ trans('admin/models/message.no_association') }}</strong> {{ trans('admin/models/message.no_association_fix') }}</p>
                </div>
            </div>
        @endif

        @if ($asset->checkInvalidNextPatchDate())
            <div class="col-md-12">
                <div class="callout callout-warning">
                    <p><strong>{{ trans('general.warning',
                        [
                            'warning' => trans('admin/assets/message.warning_patch_date_mismatch',
                                    [
                                        'last_patch_date' => Helper::getFormattedDateObject($asset->last_patch_date, 'date', false),
                                        'next_patch_date' => Helper::getFormattedDateObject($asset->next_patch_date, 'date', false)
                                    ]
                                    )
                        ]
                        ) }}</strong></p>
                </div>
            </div>
        @endif

        @if ($asset->deleted_at!='')
            <div class="col-md-12">
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle faa-pulse animated" aria-hidden="true"></i>
                    <strong>{{ trans('general.notification_warning') }} </strong>
                    {{ trans('general.asset_deleted_warning') }}
                </div>
            </div>
        @endif

        <div class="col-md-12">


            <!-- Custom Tabs -->
            <div class="nav-tabs-custom">
                <ul class="nav nav-tabs">

                    <li class="active">
                        <a href="#details" data-toggle="tab">
                          <span class="hidden-lg hidden-md">
                          <i class="fas fa-info-circle fa-2x"></i>
                          </span>
                          <span class="hidden-xs hidden-sm">{{ trans('admin/users/general.info') }}</span>
                        </a>
                    </li>

                    <li>
                        <a href="#software" data-toggle="tab">
                          <span class="hidden-lg hidden-md">
                            <i class="far fa-save fa-2x" aria-hidden="true"></i>
                          </span>
                          <span class="hidden-xs hidden-sm">{{ trans('general.licenses') }}
                            {!! ($asset->licenses->count() > 0 ) ? '<span class="badge badge-secondary">'.number_format($asset->licenses->count()).'</span>' : '' !!}
                          </span>
                        </a>
                    </li>

                    <li>
                        <a href="#history" data-toggle="tab">
                          <span class="hidden-lg hidden-md">
                            <i class="fas fa-history fa-2x" aria-hidden="true"></i>
                          </span>
                          <span class="hidden-xs hidden-sm">{{ trans('general.history') }}
                          </span>
                        </a>
                    </li>

                    @can('update', \App\Models\Asset::class)
                        <li class="pull-right">
                            <a href="#" data-toggle="modal" data-target="#uploadFileModal">
                                <i class="fas fa-paperclip" aria-hidden="true"></i>
                                {{ trans('button.upload') }}
                            </a>
                        </li>
                    @endcan
                </ul>

                <div class="tab-content">

                    <div class="tab-pane fade in active" id="details">
                        <div class="row">
                            <div class="col-md-8">

                                <!-- start striped rows -->
                                <div class="container row-striped">

                                    @if ($asset->deleted_at!='')
                                        <div class="row">
                                            <div class="col-md-2">
                                                <span class="text-danger"><strong>{{ trans('general.deleted') }}</strong></span>
                                            </div>
                                            <div class="col-md-6">
                                                {{ \App\Helpers\Helper::getFormattedDateObject($asset->deleted_at, 'date', false) }}

                                            </div>
                                        </div>
                                    @endif

                                    @if ($asset->assetstatus)
                                        <div class="row">
                                            <div class="col-md-2">
                                                <strong>{{ trans('general.status') }}</strong>
                                            </div>
                                            <div class="col-md-6">
                                                    @if (($asset->assetstatus) && ($asset->assetstatus->deployable=='1'))
                                                        <i class="fas fa-circle text-green"></i>
                                                    @elseif (($asset->assetstatus) && ($asset->assetstatus->pending=='1'))
                                                        <i class="fas fa-circle text-orange"></i>
                                                    @else
                                                        <i class="fas fa-times text-red"></i>
                                                    @endif
                                                    <a href="{{ route('statuslabels.show', $asset->assetstatus->id) }}">
                                                        {{ $asset->assetstatus->name }}</a>
                                                    <label class="label label-default">{{ $asset->present()->statusMeta }}</label>
                                            </div>
                                        </div>
                                    @endif

                                    @if ($asset->company)
                                        <div class="row">
                                            <div class="col-md-2">
                                                <strong>{{ trans('general.company') }}</strong>
                                            </div>
                                            <div class="col-md-6">
                                                <a href="{{ url('/companies/' . $asset->company->id) }}">{{ $asset->company->name }}</a>
                                            </div>
                                        </div>
                                    @endif

                                    @if ($asset->name)
                                        <div class="row">
                                            <div class="col-md-2">
                                                <strong>{{ trans('admin/assets/form.name') }}</strong>
                                            </div>
                                            <div class="col-md-6">
                                                {{ $asset->name }}
                                            </div>
                                        </div>
                                    @endif

                                    @if ($asset->serial)
                                        <div class="row">
                                            <div class="col-md-2">
                                                <strong>{{ trans('admin/assets/form.serial') }}</strong>
                                            </div>
                                            <div class="col-md-6">
                                                <span class="js-copy">{{ $asset->serial  }}</span>

                                                <i class="fa-regular fa-clipboard js-copy-link" data-clipboard-target=".js-copy" aria-hidden="true" data-tooltip="true" data-placement="top" title="{{ trans('general.copy_to_clipboard') }}">
                                                    <span class="sr-only">{{ trans('general.copy_to_clipboard') }}</span>
                                                </i>
                                            </div>
                                        </div>
                                    @endif

                                    @if ((isset($patch_log)) && ($patch_log->created_at)) 
                                        <div class="row">
                                            <div class="col-md-2">
                                                <strong>
                                                    {{ trans('general.last_patch') }}
                                                </strong>
                                            </div>
                                            <div class="col-md-6">
                                                {!! $asset->checkInvalidNextPatchDate() ? '<i class="fas fa-exclamation-triangle text-orange" aria-hidden="true"></i>' : '' !!}
                                                {{ Helper::getFormattedDateObject($patch_log->created_at, 'date', false) }}
                                                @if ($patch_log->user)
                                                    (by {{ link_to_route('users.show', $patch_log->user->present()->fullname(), [$patch_log->user->id]) }})
                                                @endif

                                            </div>
                                        </div>
                                    @endif

                                    {{-- @if ($asset->next_patch_date) --}}
                                    <div class="row">
                                        <div class="col-md-2">
                                            <strong>
                                                {{ trans('general.next_patch_date') }}
                                            </strong>
                                        </div>
                                        <div class="col-md-6">
                                            {!! $asset->checkInvalidNextPatchDate() ? '<i class="fas fa-exclamation-triangle text-orange" aria-hidden="true"></i>' : '' !!}
                                            {{ Helper::getFormattedDateObject($asset->next_patch_date, 'date', false) }}
                                        </div>
                                    </div>
                                    {{-- @endif --}}

                                    @if (($asset->model) && ($asset->model->manufacturer))
                                        <div class="row">
                                            <div class="col-md-2">
                                                <strong>
                                                    {{ trans('admin/assets/form.manufacturer') }}
                                                </strong>
                                            </div>
                                            <div class="col-md-6">
                                                <ul class="list-unstyled">
                                                    @can('view', \App\Models\Manufacturer::class)

                                                        <li>
                                                            <a href="{{ route('manufacturers.show', $asset->model->manufacturer->id) }}">
                                                                {{ $asset->model->manufacturer->name }}
                                                            </a>
                                                        </li>

                                                    @else
                                                        <li> {{ $asset->model->manufacturer->name }}</li>
                                                    @endcan
                                                </ul>
                                            </div>
                                        </div>
                                    @endif

                                    <div class="row">
                                        <div class="col-md-2">
                                            <strong>
                                                {{ trans('general.category') }}
                                            </strong>
                                        </div>
                                        <div class="col-md-6">
                                            @if (($asset->model) && ($asset->model->category))

                                                @can('view', \App\Models\Category::class)

                                                    <a href="{{ route('categories.show', $asset->model->category->id) }}">
                                                        {{ $asset->model->category->name }}
                                                    </a>
                                                @else
                                                    {{ $asset->model->category->name }}
                                                @endcan
                                            @else
                                                Invalid category
                                            @endif
                                        </div>
                                    </div>

                                    @if ($asset->model)
                                        <div class="row">
                                            <div class="col-md-2">
                                                <strong>
                                                    {{ trans('admin/assets/form.model') }}
                                                </strong>
                                            </div>
                                            <div class="col-md-6">
                                                @if ($asset->model)

                                                    @can('view', \App\Models\AssetModel::class)
                                                        <a href="{{ route('models.show', $asset->model->id) }}">
                                                            {{ $asset->model->name }}
                                                        </a>
                                                    @else
                                                        {{ $asset->model->name }}
                                                    @endcan

                                                @endif
                                            </div>
                                        </div>
                                    @endif

                                    <div class="row">
                                        <div class="col-md-2">
                                            <strong>
                                                {{ trans('admin/models/table.modelnumber') }}
                                            </strong>
                                        </div>
                                        <div class="col-md-6">
                                            {{ ($asset->model) ? $asset->model->model_number : ''}}
                                        </div>
                                    </div>

                                    @if (($asset->model) && ($asset->model->fieldset))
                                        @foreach($asset->model->fieldset->fields as $field)
                                            <div class="row">
                                                <div class="col-md-2">
                                                    <strong>
                                                        {{ $field->name }}
                                                    </strong>
                                                </div>
                                                <div class="col-md-6{{ (($field->format=='URL') && ($asset->{$field->db_column_name()}!='')) ? ' ellipsis': '' }}">
                                                    @if (($field->field_encrypted=='1') && ($asset->{$field->db_column_name()}!=''))
                                                        <i class="fas fa-lock" data-tooltip="true" data-placement="top" title="{{ trans('admin/custom_fields/general.value_encrypted') }}" onclick="showHideEncValue(this)" id="text-{{ $field->id }}"></i>
                                                    @endif

                                                    @if ($field->isFieldDecryptable($asset->{$field->db_column_name()} ))
                                                        @can('assets.view.encrypted_custom_fields')
                                                            @php
                                                                $fieldSize=strlen(Helper::gracefulDecrypt($field, $asset->{$field->db_column_name()})) 
                                                            @endphp
                                                            @if ($fieldSize>0)
                                                                <span id="text-{{ $field->id }}-to-hide">{{ str_repeat('*', $fieldSize) }}</span>
                                                                <span class="js-copy-{{ $field->id }}" id="text-{{ $field->id }}-to-show" style="font-size: 0px;">
                                                                @if (($field->format=='URL') && ($asset->{$field->db_column_name()}!=''))
                                                                    <a href="{{ Helper::gracefulDecrypt($field, $asset->{$field->db_column_name()}) }}" target="_new">{{ Helper::gracefulDecrypt($field, $asset->{$field->db_column_name()}) }}</a>
                                                                @elseif (($field->format=='DATE') && ($asset->{$field->db_column_name()}!=''))
                                                                    {{ \App\Helpers\Helper::gracefulDecrypt($field, \App\Helpers\Helper::getFormattedDateObject($asset->{$field->db_column_name()}, 'date', false)) }}
                                                                @else
                                                                    {{ Helper::gracefulDecrypt($field, $asset->{$field->db_column_name()}) }}
                                                                @endif
                                                                </span>
                                                                <i class="fa-regular fa-clipboard js-copy-link" data-clipboard-target=".js-copy-{{ $field->id }}" aria-hidden="true" data-tooltip="true" data-placement="top" title="{{ trans('general.copy_to_clipboard') }}">
                                                                    <span class="sr-only">{{ trans('general.copy_to_clipboard') }}</span>
                                                                </i>
							                                @endif
                                                        @else
                                                            {{ strtoupper(trans('admin/custom_fields/general.encrypted')) }}
                                                        @endcan

                                                    @else
                                                        @if (($field->format=='BOOLEAN') && ($asset->{$field->db_column_name()}!=''))
                                                            {!! ($asset->{$field->db_column_name()} == 1) ? "<span class='fas fa-check-circle' style='color:green' />" : "<span class='fas fa-times-circle' style='color:red' />" !!}
                                                        @elseif (($field->format=='URL') && ($asset->{$field->db_column_name()}!=''))
                                                            <a href="{{ $asset->{$field->db_column_name()} }}" target="_new">{{ $asset->{$field->db_column_name()} }}</a>
                                                        @elseif (($field->format=='DATE') && ($asset->{$field->db_column_name()}!=''))
                                                            {{ \App\Helpers\Helper::getFormattedDateObject($asset->{$field->db_column_name()}, 'date', false) }}
                                                        @else
                                                            {!! nl2br(e($asset->{$field->db_column_name()})) !!}
                                                        @endif

                                                    @endif

                                                    @if ($asset->{$field->db_column_name()}=='')
                                                        &nbsp;
                                                    @endif
                                                </div>
                                            </div>
                                        @endforeach
                                    @endif
                                    
                                    @if ($asset->asset_eol_date)
                                        <div class="row">
                                            <div class="col-md-2">
                                                <strong>
                                                    {{ trans('admin/assets/form.eol_date') }}
                                                </strong>
                                            </div>
                                            <div class="col-md-6">
                                                @if ($asset->asset_eol_date)
                                                {{ Helper::getFormattedDateObject($asset->asset_eol_date, 'date', false) }}
                                                -
                                                {{ Carbon::parse($asset->asset_eol_date)->diffForHumans(['parts' => 2]) }}
                                                @else
                                                    {{ trans('general.na_no_purchase_date') }}
                                                @endif
                                                @if ($asset->eol_explicit)
                                                    <i class="fas fa-exclamation-triangle text-orange"
                                                       aria-hidden="true"
                                                       data-tooltip="true"
                                                       data-placement="top"
                                                       data-title="Explicit EOL"
                                                       title="Explicit EOL">
                                                    </i>
                                                @endif
                                            </div>
                                        </div>
                                    @endif

                                    <div class="row">
                                        <div class="col-md-2">
                                            <strong>
                                                {{ trans('admin/assets/form.notes') }}
                                            </strong>
                                        </div>
                                        <div class="col-md-6">
                                            {!! nl2br(Helper::parseEscapedMarkedownInline($asset->notes)) !!}
                                        </div>
                                    </div>

                                    @if ($asset->location)
                                        <div class="row">
                                            <div class="col-md-2">
                                                <strong>
                                                    {{ trans('general.location') }}
                                                </strong>
                                            </div>
                                            <div class="col-md-6">
                                                @can('superuser')
                                                    <a href="{{ route('locations.show', ['location' => $asset->location->id]) }}">
                                                        {{ $asset->location->name }}
                                                    </a>
                                                @else
                                                    {{ $asset->location->name }}
                                                @endcan
                                            </div>
                                        </div>
                                    @endif

                                    @if ($asset->defaultLoc)
                                        <div class="row">
                                            <div class="col-md-2">
                                                <strong>
                                                    {{ trans('admin/assets/form.default_location') }}
                                                </strong>
                                            </div>
                                            <div class="col-md-6">
                                                @can('superuser')
                                                    <a href="{{ route('locations.show', ['location' => $asset->defaultLoc->id]) }}">
                                                        {{ $asset->defaultLoc->name }}
                                                    </a>
                                                @else
                                                    {{ $asset->defaultLoc->name }}
                                                @endcan
                                            </div>
                                        </div>
                                    @endif

                                    @if ($asset->created_at!='')
                                        <div class="row">
                                            <div class="col-md-2">
                                                <strong>
                                                    {{ trans('general.created_at') }}
                                                </strong>
                                            </div>
                                            <div class="col-md-6">
                                                {{ Helper::getFormattedDateObject($asset->created_at, 'datetime', false) }}
                                            </div>
                                        </div>
                                    @endif

                                    @if ($asset->updated_at!='')
                                        <div class="row">
                                            <div class="col-md-2">
                                                <strong>
                                                    {{ trans('general.updated_at') }}
                                                </strong>
                                            </div>
                                            <div class="col-md-6">
                                                {{ Helper::getFormattedDateObject($asset->updated_at, 'datetime', false) }}
                                            </div>
                                        </div>
                                    @endif
                                </div> <!-- end row-striped -->

                            </div><!-- /col-md-8 -->

                            <div class="col-md-4">

                                @if (($asset->image) || (($asset->model) && ($asset->model->image!='')))
                                    <div class="text-center col-md-12" style="padding-bottom: 15px;">
                                        <a href="{{ ($asset->getImageUrl()) ? $asset->getImageUrl() : null }}" data-toggle="lightbox">
                                            <img src="{{ ($asset->getImageUrl()) ? $asset->getImageUrl() : null }}" class="assetimg img-responsive" alt="{{ $asset->getDisplayNameAttribute() }}">
                                        </a>
                                    </div>
                                @else
                                    <!-- generic image goes here -->
                                @endif

                                    <!-- Start side button column -->

                                    @can('update', $asset)
                                        @if ($asset->deleted_at=='')
                                        <div class="col-md-12" style="padding-top: 5px;">
                                            <a href="{{ route('hardware.edit', $asset->id) }}" class="btn btn-sm btn-primary btn-block hidden-print">
                                                {{ trans('admin/assets/general.edit') }}
                                            </a>
                                        </div>
                                        @endif
                                    @endcan

                                    @can('create', $asset)
                                        <div class="col-md-12" style="padding-top: 5px;">
                                            <a href="{{ route('clone/hardware', $asset->id) }}" class="btn btn-sm btn-primary btn-block hidden-print">
                                                {{ trans('admin/assets/general.clone') }}
                                            </a>
                                        </div>
                                    @endcan

                                    @can('patch', \App\Models\Asset::class)
                                        <div class="col-md-12" style="padding-top: 5px;">
                                            <span class="tooltip-wrapper"{!! (!$asset->model ? ' data-tooltip="true" title="'.trans('admin/assets/general.model_invalid_fix').'"' : '') !!}>
                                                <a href="{{ route('asset.patch.create', $asset->id)  }}" class="btn btn-sm btn-primary btn-block hidden-print{{ (!$asset->model ? ' disabled' : '') }}">
                                                 {{ trans('general.patch') }}
                                            </a>
                                            </span>
                                        </div>
                                    @endcan

                                    @can('delete', $asset)
                                        <div class="col-md-12" style="padding-top: 30px; padding-bottom: 30px;">
                                            @if ($asset->deleted_at=='')
                                                <button class="btn btn-sm btn-block btn-danger delete-asset" data-toggle="modal" data-title="{{ trans('general.delete') }}" data-content="{{ trans('general.sure_to_delete_var', ['item' => $asset->asset_tag]) }}" data-target="#dataConfirmModal">{{ trans('general.delete') }}
                                                </button>
                                                <span class="sr-only">{{ trans('general.delete') }}</span>
                                            @else
                                                <form method="POST" action="{{ route('restore/hardware', ['assetId' => $asset->id]) }}">
                                                    @csrf
                                                    <button class="btn btn-sm btn-warning btn-block">{{ trans('general.restore') }}</button>
                                                </form>
                                           @endif
                                        </div>
                                    @endcan

                            </div> <!-- div.col-md-4 -->
                        </div><!-- /row -->
                    </div><!-- /.tab-pane asset details -->
                 

                    <div class="tab-pane fade" id="software">
                        <div class="row">
                            <div class="col-md-12">
                                <!-- Licenses assets table -->
                                @if ($asset->licenses->count() > 0)
                                    <table class="table">
                                        <thead>
                                        <tr>
                                            <th class="col-md-4">{{ trans('general.name') }}</th>
                                            <th class="col-md-4"><span class="line"></span>{{ trans('admin/licenses/form.license_key') }}</th>
                                            <th class="col-md-4"><span class="line"></span>{{ trans('admin/licenses/form.expiration') }}</th>
                                            <th class="col-md-1"><span class="line"></span>{{ trans('table.actions') }}</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach ($asset->licenseseats as $seat)
                                            @if ($seat->license)
                                                <tr>
                                                    <td><a href="{{ route('licenses.show', $seat->license->id) }}">{{ $seat->license->name }}</a></td>
                                                    <td>
                                                        @can('viewKeys', $seat->license)
                                                            {!! nl2br(e($seat->license->serial)) !!}
                                                        @else
                                                            ------------
                                                        @endcan
                                                    </td>
                                                    <td>
                                                        {{ Helper::getFormattedDateObject($seat->license->expiration_date, 'date', false) }}
                                                    </td>
                                                    <td>
                                                        <a href="{{ route('licenses.checkin', $seat->id) }}" class="btn btn-sm bg-purple" data-tooltip="true">{{ trans('general.checkin') }}</a>
                                                    </td>
                                                </tr>
                                            @endif
                                        @endforeach
                                        </tbody>
                                    </table>
                                @else

                                    <div class="alert alert-info alert-block">
                                        <i class="fas fa-info-circle"></i>
                                        {{ trans('general.no_results') }}
                                    </div>
                                @endif
                            </div><!-- /col -->
                        </div> <!-- row -->
                    </div> <!-- /.tab-pane software -->

                    <div class="tab-pane fade" id="history">
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
                         "fileName": "export-asset-{{  $asset->id }}-history",
                         "ignoreColumn": ["actions","image","change","checkbox","icon"]
                       }'

                      data-url="{{ route('api.activity.index', ['item_id' => $asset->id, 'item_type' => 'asset']) }}"
                      data-cookie-id-table="assetHistory"
                      data-cookie="true">
                <thead>
                <tr>
                    <th data-visible="true" data-field="icon" style="width: 40px;" class="hidden-xs" data-formatter="iconFormatter">{{ trans('admin/assets/table.icon') }}</th>
                    <th data-visible="true" data-field="action_date" data-sortable="true" data-formatter="dateDisplayFormatter">{{ trans('general.date') }}</th>
                    <th data-visible="true" data-field="admin" data-formatter="usersLinkObjFormatter">{{ trans('general.admin') }}</th>
                    <th data-visible="true" data-field="action_type">{{ trans('general.action') }}</th>
                    <th class="col-sm-2" data-field="file" data-visible="false" data-formatter="fileUploadNameFormatter">{{ trans('general.file_name') }}</th>
                    <th data-visible="true" data-field="item" data-formatter="polymorphicItemFormatter">{{ trans('general.item') }}</th>
                    <th data-visible="true" data-field="target" data-formatter="polymorphicItemFormatter">{{ trans('general.target') }}</th>
                    <th data-field="note">{{ trans('general.notes') }}</th>
                    <th data-field="signature_file" data-visible="false"  data-formatter="imageFormatter">{{ trans('general.signature') }}</th>
                    <th data-visible="false" data-field="file" data-visible="false"  data-formatter="fileUploadFormatter">{{ trans('general.download') }}</th>
                    <th data-field="log_meta" data-visible="true" data-formatter="changeLogFormatter">{{ trans('admin/assets/table.changed')}}</th>
                    <th data-field="remote_ip" data-visible="false" data-sortable="true">{{ trans('admin/settings/general.login_ip') }}</th>
                    <th data-field="user_agent" data-visible="false" data-sortable="true">{{ trans('admin/settings/general.login_user_agent') }}</th>
                    <th data-field="action_source" data-visible="false" data-sortable="true">{{ trans('general.action_source') }}</th>
                </tr>
                </thead>
              </table>
            </div>
          </div> <!-- /.row -->
        </div> <!-- /.tab-pane history -->

        <div class="tab-pane fade" id="files">
          <div class="row">
            <div class="col-md-12">

              @if ($asset->uploads->count() > 0)
              <table
                      class="table table-striped snipe-table"
                      id="assetFileHistory"
                      data-pagination="true"
                      data-id-table="assetFileHistory"
                      data-search="true"
                      data-side-pagination="client"
                      data-sortable="true"
                      data-show-columns="true"
                      data-show-fullscreen="true"
                      data-show-refresh="true"
                      data-sort-order="desc"
                      data-sort-name="created_at"
                      data-show-export="true"
                      data-export-options='{
                         "fileName": "export-asset-{{ $asset->id }}-files",
                         "ignoreColumn": ["actions","image","change","checkbox","icon"]
                       }'
                                            data-cookie-id-table="assetFileHistory">
                                        <thead>
                                        <tr>
                                            <th data-visible="true" data-field="icon" data-sortable="true">{{trans('general.file_type')}}</th>
                                            <th class="col-md-2" data-searchable="true" data-visible="true" data-field="image">{{ trans('general.image') }}</th>
                                            <th class="col-md-2" data-searchable="true" data-visible="true" data-field="filename" data-sortable="true">{{ trans('general.file_name') }}</th>
                                            <th class="col-md-1" data-searchable="true" data-visible="true" data-field="filesize">{{ trans('general.filesize') }}</th>
                                            <th class="col-md-2" data-searchable="true" data-visible="true" data-field="notes" data-sortable="true">{{ trans('general.notes') }}</th>
                                            <th class="col-md-1" data-searchable="true" data-visible="true" data-field="download">{{ trans('general.download') }}</th>
                                            <th class="col-md-2" data-searchable="true" data-visible="true" data-field="created_at" data-sortable="true">{{ trans('general.created_at') }}</th>
                                            <th class="col-md-1" data-searchable="true" data-visible="true" data-field="actions">{{ trans('table.actions') }}</th>
                                        </tr>
                                        </thead>
                                        <tbody>

                                        @foreach ($asset->uploads as $file)
                                            <tr>
                                                <td><i class="{{ Helper::filetype_icon($file->filename) }} icon-med" aria-hidden="true"></i></td>
                                                <td>
                                                    @if ( Helper::checkUploadIsImage($file->get_src('assets')))
                                                        <a href="{{ route('show/assetfile', ['assetId' => $asset->id, 'fileId' =>$file->id]) }}" data-toggle="lightbox" data-type="image" data-title="{{ $file->filename }}" data-footer="{{ Helper::getFormattedDateObject($asset->created_at, 'datetime', false) }}">
                                                            <img src="{{ route('show/assetfile', ['assetId' => $asset->id, 'fileId' =>$file->id]) }}" style="max-width: 50px;">
                                                        </a>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if (Storage::exists('private_uploads/assets/'.$file->filename))
                                                    {{ $file->filename }}
                                                    @else
                                                    <del>{{ $file->filename }}</del>
                                                    @endif
                                                </td>
                                                <td data-value="{{ (Storage::exists('private_uploads/assets/'.$file->filename) ? Storage::size('private_uploads/assets/'.$file->filename) : '') }}">
                                                    {{ @Helper::formatFilesizeUnits(Storage::exists('private_uploads/assets/'.$file->filename) ? Storage::size('private_uploads/assets/'.$file->filename) : '') }}
                                                </td>
                                                <td>
                                                    @if ($file->note)
                                                        {{ $file->note }}
                                                    @endif
                                                </td>
                                                <td>
                                                    @if (($file->filename) && (Storage::exists('private_uploads/assets/'.$file->filename)))
                                                        <a href="{{ route('show/assetfile', [$asset->id, $file->id, 'download'=>'true']) }}" class="btn btn-sm btn-default">
                                                            <i class="fas fa-download" aria-hidden="true"></i>
                                                        </a>

                                                        <a href="{{ route('show/assetfile', [$asset->id, $file->id, 'inline'=>'true']) }}" class="btn btn-sm btn-default" target="_blank">
                                                            <i class="fa fa-external-link" aria-hidden="true"></i>
                                                        </a>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if ($file->created_at)
                                                        {{ Helper::getFormattedDateObject($file->created_at, 'datetime', false) }}
                                                    @endif
                                                </td>
                                                <td>
                                                    @can('update', \App\Models\Asset::class)
                                                        <a class="btn delete-asset btn-sm btn-danger btn-sm" href="{{ route('delete/assetfile', [$asset->id, $file->id]) }}" data-tooltip="true" data-title="Delete" data-content="{{ trans('general.delete_confirm', ['item' => $file->filename]) }}"><i class="fas fa-trash icon-white" aria-hidden="true"></i></a>
                                                    @endcan
                                                </td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>

                                @else

                                    <div class="alert alert-info alert-block">
                                        <i class="fas fa-info-circle"></i>
                                        {{ trans('general.no_results') }}
                                    </div>
                                @endif

                            </div> <!-- /.col-md-12 -->
                        </div> <!-- /.row -->
                    </div> <!-- /.tab-pane files -->
                </div> <!-- /. tab-content -->
            </div> <!-- /.nav-tabs-custom -->
        </div> <!-- /. col-md-12 -->
    </div> <!-- /. row -->

    @can('update', \App\Models\Asset::class)
        @include ('modals.upload-file', ['item_type' => 'asset', 'item_id' => $asset->id])
    @endcan

@stop

@section('moar_scripts')
    <script>

        $('#dataConfirmModal').on('show.bs.modal', function (event) {
            var content = $(event.relatedTarget).data('content');
            var title = $(event.relatedTarget).data('title');
            $(this).find(".modal-body").text(content);
            $(this).find(".modal-header").text(title);
        });

    </script>
    @include ('partials.bootstrap-table')

@stop
