@extends('layouts/default')

{{-- Page title --}}
@section('title')

  @if (Request::get('status')=='deleted')
    {{ trans('admin/firmware/general.view_deleted') }}
    {{ trans('admin/firmware/table.title') }}
    @else
    {{ trans('admin/firmware/general.view_firmware') }}
  @endif
@parent
@stop

{{-- Page title --}}
@section('header_right')
  @can('create', \App\Models\Firmware::class)
    <a href="{{ route('firmware.create') }}" class="btn btn-primary pull-right"> {{ trans('general.create') }}</a>
  @endcan

  @if (Request::get('status')=='deleted')
    <a class="btn btn-default pull-right" href="{{ route('firmware.index') }}" style="margin-right: 5px;">{{ trans('admin/firmware/general.view_firmware') }}</a>
  @else
    <a class="btn btn-default pull-right" href="{{ route('firmware.index', ['status' => 'deleted']) }}" style="margin-right: 5px;">{{ trans('admin/firmware/general.view_deleted') }}</a>
  @endif
@stop


{{-- Page content --}}
@section('content')
  <div class="row">
    <div class="col-md-12">
      <div class="box box-default">
        <div class="box-body">
          @include('partials.firmware-bulk-actions')
          <div class="table-responsive">
            <table
              data-columns="{{ \App\Presenters\FirmwarePresenter::dataTableLayout() }}"
              data-cookie-id-table="firmwareTable"
              data-pagination="true"
              data-id-table="firmwareTable"
              data-search="true"
              data-show-footer="true"
              data-side-pagination="server"
              data-show-columns="true"
              data-toolbar="#firmwareBulkEditToolbar"
              data-bulk-button-id="#bulkFirmwareEditButton"
              data-bulk-form-id="#firmwareBulkForm"
              data-show-export="true"
              data-show-refresh="true"
              data-sort-order="asc"
              id="firmwareTable"
              class="table table-striped snipe-table"
              data-url="{{ route('api.firmware.index', ['status' => request('status')]) }}"
              data-export-options='{
              "fileName": "export-firmware-{{ date('Y-m-d') }}",
              "ignoreColumn": ["actions","image","change","checkbox","checkincheckout","icon"]
            }'>
            </table>
          </div><!-- /.table-responsive -->
        </div><!-- /.box-body -->
      </div>
      {{ Form::close() }}
    </div><!-- /.box-body -->
  </div><!-- /.box -->
  </div><!-- /.row -->
  </div><!-- /.content -->
@stop

@section('moar_scripts')
{{-- @include ('partials.bootstrap-table', ['exportFile' => 'firmware-export', 'search' => true]) --}}

@stop
