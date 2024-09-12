@extends('layouts/default')

{{-- Page title --}}
@section('title')
    {{ trans('general.patch_report') }}
    @parent
@stop

{{-- Page content --}}
@section('content')

    <div class="row">
        <div class="col-md-12">
            <div class="box box-default">
                <div class="box-body">

                    <table
                            data-cookie-id-table="patchReport"
                            data-pagination="true"
                            data-id-table="patchReport"
                            data-search="true"
                            data-side-pagination="server"
                            data-show-columns="true"
                            data-show-export="true"
                            data-show-refresh="true"
                            data-sort-order="asc"
                            id="patchReport"
                            data-url="{{ route('api.activity.index', ['action_type' => 'patch']) }}"
                            class="table table-striped snipe-table"
                            data-export-options='{
                        "fileName": "activity-report-{{ date('Y-m-d') }}",
                        "ignoreColumn": ["actions","image","change","checkbox","checkincheckout","icon"]
                        }'>

                        <thead>
                        <tr>
                            <th class="col-sm-1" data-field="file" data-visible="false" data-formatter="patchImageFormatter">{{ trans('admin/hardware/table.image') }}</th>
                            <th class="col-sm-2" data-field="created_at" data-formatter="dateDisplayFormatter">{{ trans('general.patch') }}</th>
                            <th class="col-sm-2" data-field="admin" data-formatter="usersLinkObjFormatter">{{ trans('general.admin') }}</th>
                            <th class="col-sm-2" data-field="item" data-formatter="polymorphicItemFormatter">{{ trans('general.item') }}</th>
                            <th class="col-sm-1" data-field="location" data-formatter="locationsLinkObjFormatter">{{ trans('general.location') }}</th>
                            <th class="col-sm-2" data-field="next_patch_date" data-formatter="dateDisplayFormatter">{{ trans('general.next_patch_date') }}</th>
                            <th class="col-sm-1" data-field="days_to_next_patch">{{ trans('general.days_to_next_patch') }}</th>

                            <th class="col-sm-2" data-field="note">{{ trans('general.notes') }}</th>
                        </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
@stop


@section('moar_scripts')
    @include ('partials.bootstrap-table')
@stop
