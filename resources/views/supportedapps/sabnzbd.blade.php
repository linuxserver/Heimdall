<h2>{{ __('app.apps.config') }} ({{ __('app.optional') }})</h2>
<div class="items">
    <input type="hidden" data-config="type" class="config-item" name="config[type]" value="\App\SupportedApps\Sabnzbd" />
    <div class="input">
        <label>{{ __('app.apps.apikey') }}</label>
        {!! Form::text('config[apikey]', null, array('placeholder' => __('app.apps.apikey'), 'data-config' => 'apikey', 'class' => 'form-control config-item')) !!}
    </div>
    <button id="test_config">Test</button>
</div>