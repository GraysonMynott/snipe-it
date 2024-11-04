<!-- MAC Address -->
<div class="form-group {{ $errors->has('mac_address') ? ' has-error' : '' }}">
    <label for="{{ $fieldname }}" class="col-md-3 control-label">{{ trans('admin/assets/form.mac_address') }} </label>
    <div class="col-md-7 col-sm-12{{  (Helper::checkIfRequired($item, 'mac_address')) ? ' required' : '' }}">
        <input class="form-control" type="text" name="{{ $fieldname }}" id="{{ $fieldname }}" value="{{ old((isset($old_val_name) ? $old_val_name : $fieldname), $item->mac_address) }}" />
        {!! $errors->first('mac_address', '<span class="alert-msg" aria-hidden="true"><i class="fas fa-times" aria-hidden="true"></i> :message</span>') !!}
        @foreach ($errors->get('mac_address') as $error)
                <p>{{ $error }}</p>
        @endforeach
    </div>
</div>
