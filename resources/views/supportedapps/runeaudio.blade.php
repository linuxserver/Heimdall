<h2>{{ __('app.apps.config') }} ({{ __('app.optional') }})</h2>
<div class="items">
    <input type="hidden" data-config="type" class="config-item" name="config[type]" value="\App\SupportedApps\Runeaudio" />
    <input type="hidden" data-config="dataonly" class="config-item" name="config[dataonly]" value="1" />
    <div class="input">
        <label>{{ strtoupper(__('app.url')) }}</label>
        {!! Form::text('config[override_url]', null, array('placeholder' => __('app.apps.override'), 'id' => 'override_url', 'class' => 'form-control')) !!}
    </div>
    
    <div class="input">
        <label>{{ __('app.apps.enable') }}</label>
        {!! Form::hidden('config[enabled]', '0') !!}
        <label class="switch">
            <?php
            $checked = false;
            if(isset($item->config->enabled) && (bool)$item->config->enabled === true) $checked = true;
            $set_checked = ($checked) ? ' checked="checked"' : '';
            ?>
            <input type="checkbox" name="config[enabled]" value="1"<?php echo $set_checked;?> />
            <span class="slider round"></span>
        </label>
    </div>
    <div class="input">
        <button style="margin-top: 32px;" class="btn test" id="test_config">Test</button>
    </div>
</div>
