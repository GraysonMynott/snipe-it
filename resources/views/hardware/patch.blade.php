@extends('layouts/default')

{{-- Page title --}}
@section('title')
    {{ trans('general.patch') }}
    @parent
@stop

{{-- Page content --}}
@section('content')

    <style>

        .input-group {
            padding-left: 0px !important;
        }
    </style>

    <div class="row">
        <!-- left column -->
        <div class="col-md-8 col-md-offset-2">
            <div class="box box-default">

                {{ Form::open([
                  'method' => 'POST',
                  'route' => ['asset.patch.store', $asset->id],
                  'files' => true,
                  'class' => 'form-horizontal' ]) }}

                    <div class="box-header with-border">
                        <h2 class="box-title"> {{ trans('admin/hardware/form.tag') }} {{ $asset->asset_tag }}</h2>
                    </div>
                    <div class="box-body">
                    {{csrf_field()}}

                        <!-- Asset model -->
                            <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                                <label class="col-sm-3 control-label">
                                    {{ trans('admin/hardware/form.model') }}
                                </label>
                                <div class="col-md-8">
                                    <p class="form-control-static">
                                        @if (($asset->model) && ($asset->model->name))
                                            {{ $asset->model->name }}
                                        @else
                                            <span class="text-danger text-bold">
                                              <i class="fas fa-exclamation-triangle" aria-hidden="true"></i>
                                              {{ trans('admin/hardware/general.model_invalid')}}
                                            </span>
                                            {{ trans('admin/hardware/general.model_invalid_fix')}}
                                            <a href="{{ route('hardware.edit', $asset->id) }}">
                                                <strong>{{ trans('admin/hardware/general.edit') }}</strong>
                                            </a>
                                        @endif
                                    </p>
                                </div>
                            </div>


                    <!-- Asset Name -->
                        <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                            <label for="name" class="col-sm-3 control-label">
                                {{ trans('general.name') }}
                            </label>
                            <div class="col-md-8">
                                <p class="form-control-static">{{ $asset->name }}</p>
                            </div>
                        </div>

                        <!-- Locations -->
                    @include ('partials.forms.edit.location-select', ['translated_name' => trans('general.location'), 'fieldname' => 'location_id'])

                    <!-- Update location -->
                        <div class="form-group">

                            <div class="col-md-8 col-md-offset-3">
                                <label class="form-control">
                                    <input type="checkbox" value="1" name="update_location" {{ old('update_location') == '1' ? ' checked="checked"' : '' }}> {{ trans('admin/hardware/form.asset_location') }}
                                </label>
                                <p class="help-block">{!! trans('help.patch_help') !!}</p>
                            </div>

                        </div>


                        <!-- Show last patch date -->
                        <div class="form-group">
                            <label class="control-label col-md-3">
                                {{ trans('general.last_patch') }}
                            </label>
                            <div class="col-md-8">

                                <p class="form-control-static">
                                    @if ($asset->last_patch_date)
                                        {{ Helper::getFormattedDateObject($asset->last_patch_date, 'datetime', false) }}
                                    @else
                                        {{ trans('admin/settings/general.none') }}
                                    @endif
                                </p>
                            </div>
                        </div>


                        <!-- Next Patch -->
                        <div class="form-group{{ $errors->has('next_patch_date') ? ' has-error' : '' }}">
                            <label for="next_patch_date" class="col-sm-3 control-label">
                                {{ trans('general.next_patch_date') }}
                            </label>
                            <div class="col-md-8">
                                <div class="input-group date col-md-5" data-provide="datepicker" data-date-format="yyyy-mm-dd" data-date-clear-btn="true">
                                    <input type="text" class="form-control" placeholder="{{ trans('general.next_patch_date') }}" name="next_patch_date" id="next_patch_date" value="{{ old('next_patch_date', $next_patch_date) }}">
                                    <span class="input-group-addon"><i class="fas fa-calendar" aria-hidden="true"></i></span>
                                </div>
                                {!! $errors->first('next_patch_date', '<span class="alert-msg" aria-hidden="true"><i class="fas fa-times" aria-hidden="true"></i> :message</span>') !!}
                                 <p class="help-block">{!! trans('general.next_patch_date_help') !!}</p>
                            </div>
                        </div>


                        <!-- Note -->
                        <div class="form-group{{ $errors->has('note') ? ' has-error' : '' }}">
                            <label for="note" class="col-sm-3 control-label">
                                {{ trans('general.notes') }}
                            </label>
                            <div class="col-md-8">
                                <textarea class="col-md-6 form-control" id="note" name="note">{{ old('note', $asset->note) }}</textarea>
                                {!! $errors->first('note', '<span class="alert-msg" aria-hidden="true"><i class="fas fa-times" aria-hidden="true"></i> :message</span>') !!}
                            </div>
                        </div>

                        <!-- Patch Image -->
                        @include ('partials.forms.edit.image-upload', ['help_text' => trans('general.patch_images_help')])


                    </div> <!--/.box-body-->
                    <div class="box-footer">
                        <a class="btn btn-link" href="{{ URL::previous() }}"> {{ trans('button.cancel') }}</a>
                        <button type="submit" class="btn btn-success pull-right{{ (!$asset->model ? ' disabled' : '') }}"{!! (!$asset->model ? ' data-tooltip="true" title="'.trans('admin/hardware/general.model_invalid_fix').'" disabled' : '') !!}>
                            <i class="fas fa-check icon-white" aria-hidden="true"></i>
                            {{ trans('general.patch') }}
                        </button>
                    </div>
                </form>
            </div>
        </div> <!--/.col-md-7-->
    </div>
@stop
