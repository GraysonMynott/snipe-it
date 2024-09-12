@extends('layouts/default')

{{-- Page title --}}
@section('title')
{{ trans('general.license_report') }} 
@parent
@stop

{{-- Page content --}}
@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="box box-default">
            <div class="box-body">
                <div class="table-responsive">

                    <table
                            data-cookie-id-table="licensesReport"
                            data-pagination="true"
                            data-id-table="licensesReport"
                            data-search="true"
                            data-side-pagination="client"
                            data-show-columns="true"
                            data-show-export="true"
                            data-show-refresh="true"
                            data-sort-order="asc"
                            id="licensesReport"
                            class="table table-striped snipe-table"
                            data-export-options='{
                        "fileName": "license-report-{{ date('Y-m-d') }}",
                        "ignoreColumn": ["actions","image","change","checkbox","checkincheckout","icon"]
                        }'>
                        <thead>
                            <tr role="row">
                                <th class="col-sm-1">{{ trans('admin/companies/table.title') }}</th>
                                <th class="col-sm-1">{{ trans('admin/licenses/table.title') }}</th>
                                <th class="col-sm-1">{{ trans('admin/licenses/form.license_key') }}</th>
                                <th class="col-sm-1">{{ trans('admin/licenses/form.seats') }}</th>
                                <th class="col-sm-1">{{ trans('admin/licenses/form.remaining_seats') }}</th>
                                <th class="col-sm-1">{{ trans('admin/licenses/form.expiration') }}</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach ($licenses as $license)
                            <tr>
                                <td>{{ is_null($license->company) ? '' : $license->company->name }}</td>
                                <td>{{ $license->name }}</td>
                                <td>
                                    @can('viewKeys', $license)
                                        {{ $license->serial }}
                                    @else
                                        ------------
                                    @endcan
                                </td>
                                <td>{{ $license->seats }}</td>
                                <td>{{ $license->remaincount() }}</td>
                                <td>{{ $license->expiration_date }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div> <!-- /.table-responsive-->
            </div>
        </div>
    </div>
</div>

@stop

@section('moar_scripts')
    @include ('partials.bootstrap-table')
@stop
