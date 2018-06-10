<h2>{{ __('app.apps.config') }} ({{ __('app.optional') }})</h2>
<div class="items">
    <input type="hidden" name="config[enabled]" value="1" />
    <input type="hidden" data-config="type" class="config-item" name="config[type]" value="\App\SupportedApps\Transmission" />
    <div class="input">
        <label>{{ strtoupper(__('app.url')) }}</label>
        {!! Form::text('config[override_url]', null, array('placeholder' => __('app.apps.override'), 'id' => 'override_url', 'class' => 'form-control')) !!}
    </div>
    <div class="input">
        <label>{{ __('app.apps.username') }}</label>
        {!! Form::text('config[username]', null, array('placeholder' => __('app.apps.username'), 'data-config' => 'username', 'class' => 'form-control config-item')) !!}
    </div>
    <div class="input">
        <label>{{ __('app.apps.password') }}</label>
        {!! Form::text('config[password]', null, array('placeholder' => __('app.apps.password'), 'data-config' => 'password', 'class' => 'form-control config-item')) !!}
    </div>
    <div class="input">
        <button style="margin-top: 32px;" class="btn test" id="test_config">Test</button>
    </div>
</div>
