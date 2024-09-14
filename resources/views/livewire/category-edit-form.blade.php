<div>
    <!-- EULA text -->
    <div class="form-group {{ $errors->has('eula_text') ? 'error' : '' }}">
        <label for="eula_text" class="col-md-3 control-label">{{ trans('admin/categories/general.eula_text') }}</label>
        <div class="col-md-7">
            {{ Form::textarea('eula_text', null, ['wire:model.live' => 'eulaText', 'class' => 'form-control', 'aria-label'=>'eula_text', 'disabled' => $this->eulaTextDisabled]) }}
            <p class="help-block">{!! trans('admin/categories/general.eula_text_help') !!} </p>
            <p class="help-block">{!! trans('admin/settings/general.eula_markdown') !!} </p>
            {!! $errors->first('eula_text', '<span class="alert-msg" aria-hidden="true">:message</span>') !!}
        </div>
        @if ($this->eulaTextDisabled)
            <input type="hidden" name="eula_text" wire:model.live="eulaText" />
        @endif
    </div>

    <!-- Use default checkbox -->
    <div class="form-group">
        <div class="col-md-9 col-md-offset-3">
            @if ($defaultEulaText!='')
                <label class="form-control">
                    {{ Form::checkbox('use_default_eula', '1', $useDefaultEula, ['wire:model.live' => 'useDefaultEula', 'aria-label'=>'use_default_eula']) }}
                    <span>{!! trans('admin/categories/general.use_default_eula') !!}</span>
                </label>
            @else
                <label class="form-control form-control--disabled">
                    {{ Form::checkbox('use_default_eula', '0', $useDefaultEula, ['wire:model.live' => 'useDefaultEula', 'class'=>'disabled','disabled' => 'disabled', 'aria-label'=>'use_default_eula']) }}
                    <span>{!! trans('admin/categories/general.use_default_eula_disabled') !!}</span>
                </label>
            @endif
        </div>
    </div>
</div>
