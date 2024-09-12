{{-- License Create/Edit Page --}}

@extends('layouts/edit-form', [
    'createText' => trans('admin/licenses/form.create'),
    'updateText' => trans('admin/licenses/form.update'),
    'topSubmit' => true,
    'formAction' => ($item->id) ? route('licenses.update', ['license' => $item->id]) : route('licenses.store'),
])

{{-- Page content --}}
@section('inputFields')

<!-- Software Name -->
@include ('partials.forms.edit.name', ['translated_name' => trans('admin/licenses/form.name')])

<!-- Software Category -->
@include ('partials.forms.edit.category-select', ['translated_name' => trans('admin/categories/general.category_name'), 'fieldname' => 'category_id', 'required' => 'true', 'category_type' => 'license'])

<!-- License Serial -->
@can('viewKeys', $item)
<div class="form-group {{ $errors->has('serial') ? ' has-error' : '' }}">
    <label for="serial" class="col-md-3 control-label">{{ trans('admin/licenses/form.license_key') }}</label>
    <div class="col-md-7{{  (Helper::checkIfRequired($item, 'serial')) ? ' required' : '' }}">
        <textarea class="form-control" type="text" name="serial" id="serial">{{ old('serial', $item->serial) }}</textarea>
        {!! $errors->first('serial', '<span class="alert-msg" aria-hidden="true"><i class="fas fa-times" aria-hidden="true"></i> :message</span>') !!}
    </div>
</div>
@endcan


<!-- Seats -->
<div class="form-group {{ $errors->has('seats') ? ' has-error' : '' }}">
    <label for="seats" class="col-md-3 control-label">{{ trans('admin/licenses/form.seats') }}</label>
    <div class="col-md-7 col-sm-12 required">
        <div class="col-md-2" style="padding-left:0px">
            <input class="form-control" type="text" name="seats" id="seats" value="{{ old('seats', $item->seats) }}" />
        </div>
    </div>
    {!! $errors->first('seats', '<div class="col-md-8 col-md-offset-3"><span class="alert-msg" aria-hidden="true"><i class="fas fa-times" aria-hidden="true"></i> :message</span></div>') !!}
</div>

<!-- Company -->
@include ('partials.forms.edit.company-select', ['translated_name' => trans('general.company'), 'fieldname' => 'company_id'])

<!-- Manufacturer/Software Provider -->
@include ('partials.forms.edit.manufacturer-select', ['translated_name' => trans('general.manufacturer'), 'fieldname' => 'manufacturer_id',])

<!-- Reassignable -->
<div class="form-group {{ $errors->has('reassignable') ? ' has-error' : '' }}">
    <div class="col-md-3 control-label">
        <strong>{{ trans('admin/licenses/form.reassignable') }}</strong>
    </div>
    <div class="col-md-7">
        <label class="form-control">
        {{ Form::Checkbox('reassignable', '1', old('reassignable', $item->id ? $item->reassignable : '1'),array('aria-label'=>'reassignable')) }}
        {{ trans('general.yes') }}
        </label>
    </div>
</div>

<!-- Expiration Date -->
<div class="form-group {{ $errors->has('expiration_date') ? ' has-error' : '' }}">
    <label for="expiration_date" class="col-md-3 control-label">{{ trans('admin/licenses/form.expiration') }}</label>

    <div class="input-group col-md-4">
        <div class="input-group date" data-provide="datepicker" data-date-format="yyyy-mm-dd"  data-autoclose="true" data-date-clear-btn="true">
            <input type="text" class="form-control" placeholder="{{ trans('general.select_date') }}" name="expiration_date" id="expiration_date" value="{{ old('expiration_date', ($item->expiration_date) ? $item->expiration_date->format('Y-m-d') : '') }}" maxlength="10">
            <span class="input-group-addon"><i class="fas fa-calendar" aria-hidden="true"></i></span>
        </div>
        {!! $errors->first('expiration_date', '<span class="alert-msg" aria-hidden="true"><i class="fas fa-times" aria-hidden="true"></i> :message</span>') !!}
    </div>
</div>

<!-- Termination Date -->
<div class="form-group {{ $errors->has('termination_date') ? ' has-error' : '' }}">
    <label for="termination_date" class="col-md-3 control-label">{{ trans('admin/licenses/form.termination_date') }}</label>

    <div class="input-group col-md-4">
        <div class="input-group date" data-provide="datepicker" data-date-format="yyyy-mm-dd" data-autoclose="true" data-date-clear-btn="true">
            <input type="text" class="form-control" placeholder="{{ trans('general.select_date') }}" name="termination_date" id="termination_date" value="{{ old('termination_date', ($item->termination_date) ? $item->termination_date->format('Y-m-d') : '') }}" maxlength="10">
            <span class="input-group-addon"><i class="fas fa-calendar" aria-hidden="true"></i></span>
        </div>
        {!! $errors->first('termination_date', '<span class="alert-msg" aria-hidden="true"><i class="fas fa-times" aria-hidden="true"></i> :message</span>') !!}
    </div>
</div>

<!-- Notes -->
@include ('partials.forms.edit.notes')

@stop
