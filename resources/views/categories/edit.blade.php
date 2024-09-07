@extends('layouts/edit-form', [
    'createText' => trans('admin/categories/general.create') ,
    'updateText' => trans('admin/categories/general.update'),
    'helpPosition'  => 'right',
    'helpText' => trans('help.categories'),
    'topSubmit'  => 'true',
    'formAction' => (isset($item->id)) ? route('categories.update', ['category' => $item->id]) : route('categories.store'),
])

@section('inputFields')

@include ('partials.forms.edit.name', ['translated_name' => trans('admin/categories/general.name')])

<!-- Type -->
<div class="form-group {{ $errors->has('category_type') ? ' has-error' : '' }}">
    <label for="category_type" class="col-md-3 control-label">{{ trans('general.type') }}</label>
    <div class="col-md-7 required">
        {{ Form::select('category_type', $category_types , old('category_type', $item->category_type), array('class'=>'select2', 'style'=>'min-width:350px', 'aria-label'=>'category_type', ($item->category_type!='') || ($item->itemCount() > 0) ? 'disabled' : '')) }}
        {!! $errors->first('category_type', '<span class="alert-msg" aria-hidden="true"><i class="fas fa-times" aria-hidden="true"></i> :message</span>') !!}
    </div>
    <div class="col-md-7 col-md-offset-3">
        <p class="help-block">{!! trans('admin/categories/message.update.cannot_change_category_type') !!} </p>
    </div>
</div>

@include ('partials.forms.edit.image-upload', ['image_path' => app('categories_upload_path')])


@stop

@section('content')
@parent


@stop
