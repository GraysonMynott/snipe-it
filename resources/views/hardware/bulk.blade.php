@extends('layouts/default')

{{-- Page title --}}
@section('title')
{{ trans('admin/assets/form.update') }}
@parent
@stop


@section('header_right')
<a href="{{ URL::previous() }}" class="btn btn-sm btn-primary pull-right">
  {{ trans('general.back') }}</a>
@stop

{{-- Page content --}}
@section('content')
<div class="row">
  <div class="col-md-8 col-md-offset-2">

    <p>{{ trans('admin/assets/form.bulk_update_help') }}</p>

    <form class="form-horizontal" method="post" action="{{ route('hardware/bulksave') }}" autocomplete="off" role="form">
      {{ csrf_field() }}

      <div class="box box-default">
        <div class="box-body">

          <div class="callout callout-warning">
            <i class="fas fa-exclamation-triangle"></i> {{ trans_choice('admin/assets/form.bulk_update_warn', count($assets), ['asset_count' => count($assets)]) }}

            @if (count($models) > 0)
              {{ trans_choice('admin/assets/form.bulk_update_with_custom_field', count($models), ['asset_model_count' => count($models)]) }}
            @endif
          </div>

          <!-- Status -->
          <div class="form-group {{ $errors->has('status_id') ? ' has-error' : '' }}">
            <label for="status_id" class="col-md-3 control-label">
              {{ trans('admin/assets/form.status') }}
            </label>
            <div class="col-md-7">
              {{ Form::select('status_id', $statuslabel_list , old('status_id'), array('class'=>'select2', 'style'=>'width:100%', 'aria-label'=>'status_id')) }}
              <p class="help-block">{{ trans('general.status_compatibility') }}</p>
              {!! $errors->first('status_id', '<span class="alert-msg" aria-hidden="true"><i class="fas fa-times" aria-hidden="true"></i> :message</span>') !!}
            </div>
          </div>

        @include ('partials.forms.edit.model-select', ['translated_name' => trans('admin/assets/form.model'), 'fieldname' => 'model_id'])

          <!-- Default Location -->
        @include ('partials.forms.edit.location-select', ['translated_name' => trans('admin/assets/form.default_location'), 'fieldname' => 'rtd_location_id'])

        <!-- Update actual location  -->
          <div class="form-group">
            <div class="col-md-9 col-md-offset-3">
                <label class="form-control">
                  {{ Form::radio('update_real_loc', '1', old('update_real_loc'), ['checked'=> 'checked', 'aria-label'=>'update_real_loc']) }}
                  {{ trans('admin/assets/form.asset_location_update_default_current') }}
                </label>
              <label class="form-control">
                {{ Form::radio('update_real_loc', '0', old('update_real_loc'), ['aria-label'=>'update_default_loc']) }}
                {{ trans('admin/assets/form.asset_location_update_default') }}
              </label>
                <label class="form-control">
                  {{ Form::radio('update_real_loc', '2', old('update_real_loc'), ['aria-label'=>'update_default_loc']) }}
                  {{ trans('admin/assets/form.asset_location_update_actual') }}
                </label>

            </div>
          </div> <!--/form-group-->

          <!-- Company -->
          @include ('partials.forms.edit.company-select', ['translated_name' => trans('general.company'), 'fieldname' => 'company_id'])

          <!-- Next Patch Date -->
          <div class="form-group {{ $errors->has('next_patch_date') ? ' has-error' : '' }}">
            <label for="next_patch_date" class="col-md-3 control-label">{{ trans('general.next_patch_date') }}</label>
            <div class="col-md-4">
              <div class="input-group date" data-provide="datepicker" data-date-format="yyyy-mm-dd"  data-autoclose="true">
                <input type="text" class="form-control" placeholder="{{ trans('general.select_date') }}" name="next_patch_date" id="next_patch_date" value="{{ old('next_patch_date') }}">
                <span class="input-group-addon"><i class="fas fa-calendar" aria-hidden="true"></i></span>
              </div>

              {!! $errors->first('next_patch_date', '<span class="alert-msg" aria-hidden="true">
                <i class="fas fa-times" aria-hidden="true"></i> :message</span>') !!}
            </div>
            <div class="col-md-5">
              <label class="form-control">
                {{ Form::checkbox('null_next_patch_date', '1', false) }}
                {{ trans_choice('general.set_to_null', count($assets), ['asset_count' => count($assets)]) }}
              </label>
            </div>
            <div class="col-md-8 col-md-offset-3">
              <p class="help-block">{!! trans('general.next_patch_date_help') !!}</p>
            </div>
          </div>

          @include("models/custom_fields_form_bulk_edit",["models" => $models])

          @foreach($assets as $asset)
            <input type="hidden" name="ids[]" value="{{ $asset }}">
          @endforeach
        </div> <!--/.box-body-->

        <div class="text-right box-footer">
          <button type="submit" class="btn btn-success"><i class="fas fa-check icon-white" aria-hidden="true"></i> {{ trans('general.save') }}</button>
        </div>
      </div> <!--/.box.box-default-->
    </form>
  </div> <!--/.col-md-8-->
</div>
@stop
