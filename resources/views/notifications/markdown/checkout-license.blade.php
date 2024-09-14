@component('mail::message')
# {{ trans('mail.hello') }} {{ $target->present()->fullName() }},

{{ trans('mail.new_item_checked') }}

@component('mail::table')
|        |          |
| ------------- | ------------- |
@if (isset($checkout_date))
| **{{ trans('mail.checkout_date') }}** | {{ $checkout_date }} |
@endif
| **{{ trans('general.license') }}** | {{ $item->name }} |
@if (isset($item->manufacturer))
| **{{ trans('general.manufacturer') }}** | {{ $item->manufacturer->name }} |
@endif
@if (isset($item->category))
| **{{ trans('general.category') }}** | {{ $item->category->name }} |
@endif
@if ($target->can('view', $item))
| **Key** | {{ $item->serial }} |
@endif
@if ($note)
| **{{ trans('mail.additional_notes') }}** | {{ $note }} |
@endif
@if ($admin)
| **{{ trans('general.administrator') }}** | {{ $admin->present()->fullName() }} |
@endif
@endcomponent


{{ trans('mail.best_regards') }}

{{ $snipeSettings->site_name }}

@endcomponent
