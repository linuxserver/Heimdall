<h2>{{ __('app.apps.config') }} ({{ __('app.optional') }})</h2>
<div class="items">
    <input type="hidden" name="config[enabled]" value="1" />
    <input type="hidden" data-config="dataonly" class="config-item" name="config[dataonly]" value="1" />
    <input type="hidden" data-config="type" class="config-item" name="config[type]" value="\App\SupportedApps\CouchPotato" />
    <div class="input">
        <label>{{ __('app.apps.apikey') }}</label>
        {!! Form::text('config[apikey]', null, array('placeholder' => __('app.apps.apikey'), 'data-config' => 'apikey', 'class' => 'form-control config-item')) !!}
    </div>
    <div class="input">
        <button style="margin-top: 32px;" class="btn test" id="test_config">Test</button>
    </div>
</div>
