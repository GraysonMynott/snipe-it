@component('mail::message')

### {{ trans_choice('mail.upcoming-patches', $assets->count(), ['count' => $assets->count(), 'threshold' => $threshold]) }}

@component('mail::table')
| |{{ trans('mail.name') }}|{{ trans('general.last_patch') }}|{{ trans('general.next_patch_date') }}|{{ trans('mail.Days') }}|| {{ trans('mail.assigned_to') }}|{{ trans('general.notes') }}
|-|:------------- |:-------------|:---------|:---------|:---------|:---------|:---------|
@foreach ($assets as $asset)
@php
$next_patch_date = Helper::getFormattedDateObject($asset->next_patch_date, 'date', false);
$last_patch_date = Helper::getFormattedDateObject($asset->last_patch_date, 'date', false);
$diff = Carbon::parse(Carbon::now())->diffInDays($asset->next_patch_date, false);
$icon = ($diff <= 7) ? '🚨' : (($diff <= 14) ? '⚠️' : ' ');
@endphp
|{{ $icon }}| [{{ $asset->present()->name }}]({{ route('hardware.show', $asset->id) }}) | {{ $last_patch_date }}| {{ $next_patch_date }} | {{ $diff }}  |{{ ($asset->assignedTo ? $asset->assignedTo->present()->name() : '') }}|{{ $asset->notes }}
@endforeach
@endcomponent


@endcomponent
