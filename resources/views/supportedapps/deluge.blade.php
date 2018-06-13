<h2>{{ __('app.apps.config') }} ({{ __('app.optional') }})</h2>
<div class="items">
    <input type="hidden" data-config="type" class="config-item" name="config[type]" value="\App\SupportedApps\Deluge" />
    <div class="input">
        <label>{{ __('app.apps.password') }}</label>
        {!! Form::text('config[password]', null, array('placeholder' => __('app.apps.password'), 'data-config' => 'password', 'class' => 'form-control config-item')) !!}
    </div>
    <div class="input">
        <button style="margin-top: 32px;" class="btn test" id="test_config">Test</button>
    </div>
</div>
