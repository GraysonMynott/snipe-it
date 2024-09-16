@extends('layouts/edit-form', [
    'createText' => trans('admin/locations/table.create') ,
    'updateText' => trans('admin/locations/table.update'),
    'topSubmit' => true,
    'helpPosition' => 'right',
    'helpText' => trans('admin/locations/table.about_locations'),
    'formAction' => (isset($item->id)) ? route('locations.update', ['location' => $item->id]) : route('locations.store'),
])

{{-- Page content --}}
@section('inputFields')
@include ('partials.forms.edit.name', ['translated_name' => trans('admin/locations/table.name')])

@include ('partials.forms.edit.phone')

@include ('partials.forms.edit.address')
@stop

