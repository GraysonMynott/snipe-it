@extends('layouts/default')

{{-- Page title --}}
@section('title')
{{ trans('general.depreciation_report') }}
@parent
@stop

{{-- Page content --}}
@section('content')

<div class="page-header">
    <div class="pull-right">
        <a href="{{ route('reports/export') }}" class="btn btn-flat gray pull-right"><i class="fas fa-download icon-white" aria-hidden="true"></i>
        {{ trans('admin/hardware/table.dl_csv') }}</a>
        </div>
    <h2>{{ trans('general.depreciation_report') }}</h2>
</div>

<div class="row">
    <div class="table-responsive">
        <table id="example">
            <thead>
                <tr role="row">
                    <th class="col-sm-1">{{ trans('admin/hardware/table.asset_tag') }}</th>
                    <th class="col-sm-1">{{ trans('admin/hardware/table.title') }}</th>
                    @if ($snipeSettings->display_asset_name)
                    <th class="col-sm-1">{{ trans('general.name') }}</th>
                    @endif
                    <th class="col-sm-1">{{ trans('admin/hardware/table.serial') }}</th>
                    <th class="col-sm-1">{{ trans('admin/hardware/table.location') }}</th>
                    <th class="col-sm-1">{{ trans('admin/hardware/table.eol') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($assets as $asset)
                <tr>
                    <td>{{ $asset->asset_tag }}</td>
                    <td>{{ $asset->model->name }}</td>
                    @if ($snipeSettings->display_asset_name)
                    <td>{{ $asset->name }}</td>
                    @endif
                    <td>{{ $asset->serial }}</td>
                    <td>
                        @if (($asset->checkedOutToUser()) && ($asset->assignedTo->assetLoc))
                            {{ $asset->assignedTo->assetLoc->city }}, {{ $asset->assignedTo->assetLoc->state}}
                        @endif
                    </td>
                    <td>
                        @if ($asset->model->eol) {{ $asset->present()->eol_date() }}
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

@stop
