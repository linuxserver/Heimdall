<h2>{{ __('app.apps.config') }} ({{ __('app.optional') }})</h2>
<div class="items">
    <input type="hidden" name="config[type]" value="\App\SupportedApps\Nzbget" />
    <div class="input">
        <label>{{ __('app.apps.username') }}</label>
        {!! Form::text('config[username]', null, array('placeholder' => __('app.apps.username'), 'class' => 'form-control')) !!}
    </div>
    <div class="input">
        <label>{{ __('app.apps.password') }}</label>
        {!! Form::text('config[password]', null, array('placeholder' =>  __('app.apps.password'), 'class' => 'form-control')) !!}
    </div>
</div>