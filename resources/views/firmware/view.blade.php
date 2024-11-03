@extends('layouts/default')

{{-- Page title --}}
@section('title')
    {{ $model->name }}
    {{ ($model->model_number) ? '(#'.$model->model_number.')' : '' }}
@parent
@stop

@section('header_right')
    @can('update', \App\Models\AssetModel::class)
        <div class="btn-group pull-right">
            <button class="btn btn-default dropdown-toggle" data-toggle="dropdown">{{ trans('button.actions') }}
                <span class="caret"></span>
            </button>
            <ul class="dropdown-menu">
                @if ($model->deleted_at=='')
                    <li><a href="{{ route('models.edit', $model->id) }}">{{ trans('admin/models/table.edit') }}</a></li>
                    <li><a href="{{ route('models.clone.create', $model->id) }}">{{ trans('admin/models/table.clone') }}</a></li>
                    <li><a href="{{ route('hardware.create', ['model_id' => $model->id]) }}">{{ trans('admin/assets/form.create') }}</a></li>
                @else
                    <li><a href="{{ route('models.restore.store', $model->id) }}">{{ trans('admin/models/general.restore') }}</a></li>
                @endif
            </ul>
        </div>
    @endcan
@stop

{{-- Page content --}}
@section('content')


<div class="row">
    <div class="col-md-9">
        <div class="nav-tabs-custom">

            <ul class="nav nav-tabs">
                <li class="active">
                    <a href="#assets" data-toggle="tab">

                        <span class="hidden-lg hidden-md">
                          <i class="fas fa-barcode fa-2x"></i>
                        </span>
                                    <span class="hidden-xs hidden-sm">
                                        {{ trans('general.assets') }}
                                        {!! ($model->assets()->AssetsForShow()->count() > 0 ) ? '<badge class="badge badge-secondary">'.number_format($model->assets()->AssetsForShow()->count()).'</badge>' : '' !!}
                        </span>
                    </a>
                </li>
            </ul>

            <div class="tab-content">
                <div class="tab-pane fade in active" id="assets">

                    @include('partials.asset-bulk-actions')

                    <table
                            data-columns="{{ \App\Presenters\AssetPresenter::dataTableLayout() }}"
                            data-cookie-id-table="assetListingTable"
                            data-pagination="true"
                            data-id-table="assetListingTable"
                            data-search="true"
                            data-side-pagination="server"
                            data-show-columns="true"
                            data-show-fullscreen="true"
                            data-toolbar="#assetsBulkEditToolbar"
                            data-bulk-button-id="#bulkAssetEditButton"
                            data-bulk-form-id="#assetsBulkForm"
                            data-click-to-select="true"
                            data-show-export="true"
                            data-show-refresh="true"
                            data-sort-order="asc"
                            id="assetListingTable"
                            data-url="{{ route('api.assets.index',['model_id'=> $model->id]) }}"
                            class="table table-striped snipe-table"
                            data-export-options='{
                "fileName": "export-models-{{ str_slug($model->name) }}-assets-{{ date('Y-m-d') }}",
                "ignoreColumn": ["actions","image","change","checkbox","checkincheckout","icon"]
                }'>
                    </table>
                    {{ Form::close() }}
                </div> <!-- /.tab-pane assets -->
            </div> <!-- /.tab-content -->
        </div>  <!-- /.nav-tabs-custom -->
    </div><!-- /. col-md-12 -->

    <div class="col-md-3">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-default">
                    <div class="box-header with-border">
                        <div class="box-heading">
                            <h2 class="box-title"> {{ trans('general.moreinfo') }}:</h2>
                        </div>
                    </div><!-- /.box-header -->
                    <div class="box-body">



                @if ($model->image)
                    <img src="{{ Storage::disk('public')->url(app('models_upload_path').e($model->image)) }}" class="img-responsive"></li>
                @endif


                <ul class="list-unstyled" style="line-height: 25px;">
                    @if ($model->category)
                        <li>{{ trans('general.category') }}:
                            <a href="{{ route('categories.show', $model->category->id) }}">{{ $model->category->name }}</a>
                        </li>
                    @endif

                    @if ($model->created_at)
                        <li>{{ trans('general.created_at') }}:
                            {{ Helper::getFormattedDateObject($model->created_at, 'datetime', false) }}
                        </li>
                    @endif

                    @if ($model->min_amt)
                        <li>{{ trans('general.min_amt') }}:
                           {{$model->min_amt }}
                        </li>
                    @endif

                    @if ($model->manufacturer)
                        <li>
                            {{ trans('general.manufacturer') }}:
                            @can('view', \App\Models\Manufacturer::class)
                                <a href="{{ route('manufacturers.show', $model->manufacturer->id) }}">
                                    {{ $model->manufacturer->name }}
                                </a>
                            @else
                                {{ $model->manufacturer->name }}
                            @endcan
                        </li>

                        @if ($model->manufacturer->url)
                            <li>
                                <i class="fas fa-globe-americas"></i> <a href="{{ $model->manufacturer->url }}">{{ $model->manufacturer->url }}</a>
                            </li>
                        @endif

                        @if ($model->manufacturer->support_url)
                            <li>
                                <i class="far fa-life-ring"></i> <a href="{{ $model->manufacturer->support_url }}">{{ $model->manufacturer->support_url }}</a>
                            </li>
                        @endif

                        @if ($model->manufacturer->support_phone)
                            <li>
                                <i class="fas fa-phone"></i>
                                <a href="tel:{{ $model->manufacturer->support_phone }}">{{ $model->manufacturer->support_phone }}</a>

                            </li>
                        @endif

                        @if ($model->manufacturer->support_email)
                            <li>
                                <i class="far fa-envelope"></i> <a href="mailto:{{ $model->manufacturer->support_email }}">{{ $model->manufacturer->support_email }}</a>
                            </li>
                        @endif
                    @endif
                    @if ($model->model_number)
                        <li>
                            {{ trans('general.model_no') }}:
                            {{ $model->model_number }}
                        </li>
                    @endif

                    @if ($model->eol)
                        <li>{{ trans('general.eol') }}:
                            {{ $model->eol .' '. trans('general.months') }}
                        </li>
                    @endif

                    @if ($model->fieldset)
                        <li>{{ trans('admin/models/general.fieldset') }}:
                            <a href="{{ route('fieldsets.show', $model->fieldset->id) }}">{{ $model->fieldset->name }}</a>
                        </li>
                    @endif

                    @if ($model->notes)
                        <li>
                            {{ trans('general.notes') }}:
                            {!! nl2br(Helper::parseEscapedMarkedownInline($model->notes)) !!}
                        </li>
                    @endif

                </ul>

                @if ($model->note)
                    Notes:
                    <p>
                        {!! $model->present()->note() !!}
                    </p>
                @endif
            </div>
        </div>
        </div>
            @can('update', \App\Models\AssetModel::class)
            <div class="col-md-12" style="padding-bottom: 5px;">
                <a href="{{ route('models.edit', $model->id) }}" style="width: 100%;" class="btn btn-sm btn-primary hidden-print">{{ trans('admin/models/table.edit') }}</a>
            </div>
            @endcan

            @can('create', \App\Models\AssetModel::class)
            <div class="col-md-12" style="padding-bottom: 5px;">
                <a href="{{ route('models.clone.create', $model->id) }}" style="width: 100%;" class="btn btn-sm btn-primary hidden-print">{{ trans('admin/models/table.clone') }}</a>
            </div>
            @endcan

            @can('delete', \App\Models\AssetModel::class)
                @if ($model->assets_count > 0)
                    <div class="col-md-12" style="padding-bottom: 5px;">
                        <button class="btn btn-block btn-sm btn-primary hidden-print disabled" data-tooltip="true"  data-placement="top" data-title="{{ trans('general.cannot_be_deleted') }}">{{ trans('general.delete') }}</button>
                    </div>
                @else

                @endif


               <div class="text-center col-md-12" style="padding-top: 30px; padding-bottom: 30px;">
                @if  ($model->deleted_at!='')
                    <form method="POST" action="{{ route('models.restore.store', $model->id) }}">
                        @csrf
                        <button style="width: 100%;" class="btn btn-sm btn-warning hidden-print">{{ trans('button.restore') }}</button>
                    </form>
                @else
                    <button class="btn btn-block btn-sm btn-danger delete-asset" data-toggle="modal" title="{{ trans('general.delete_what', ['item'=> trans('general.asset_model')]) }}" data-content="{{ trans('general.sure_to_delete_var', ['item' => $model->name]) }}" data-target="#dataConfirmModal" data-tooltip="true"  data-placement="top" data-title="{{ trans('general.delete_what', ['item'=> trans('general.asset_model')]) }}">{{ trans('general.delete') }} </button>
                    <span class="sr-only">{{ trans('general.delete') }}</span>
                @endif
               </div>

           @endcan
        </div>
</div> <!-- /.row -->

@can('update', \App\Models\AssetModel::class)
    @include ('modals.upload-file', ['item_type' => 'models', 'item_id' => $model->id])
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

    @include ('partials.bootstrap-table', ['exportFile' => 'manufacturer' . $model->name . '-export', 'search' => false])

@stop