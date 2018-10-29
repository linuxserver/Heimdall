<div class="toggleinput">
    <label class="name">{{ __('app.apps.enable') }}</label>
    {!! Form::hidden('config[enabled]', '0') !!}
    <label class="switch">
        <?php
        $checked = false;
        if(isset($item) && !empty($item)) $checked = $item->enabled();
        $set_checked = ($checked) ? ' checked="checked"' : '';
        ?>                   
        <input type="checkbox" name="config[enabled]" value="1"<?php echo $set_checked;?> />
        <span class="slider round"></span>
    </label>
</div>
