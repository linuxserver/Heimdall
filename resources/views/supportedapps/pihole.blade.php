<h2>{{ __('app.apps.config') }} ({{ __('app.optional') }})</h2>
<div class="items">
    <input type="hidden" data-config="type" class="config-item" name="config[type]" value="\App\SupportedApps\Pihole" />
    <input type="hidden" data-config="dataonly" class="config-item" name="config[dataonly]" value="1" />
    <div class="input">
        <label>{{ __('app.apps.enable') }}</label>
        <label class="switch">
            <?php
            $checked = false;
            if(isset($item->config->enabled) && (bool)$item->config->enabled === true) $checked = true;
            $set_checked = ($checked) ? ' checked="checked"' : '';
            ?>
            {!! Form::hidden('config[enabled]', '0') !!}
            <input type="checkbox" name="config[enabled]" value="1"<?php echo $set_checked;?> />
            <span class="slider round"></span>
        </label>
</div>
    <button id="test_config">Test</button>
</div>