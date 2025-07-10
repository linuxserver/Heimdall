<section class="module-container">
        @if($enable_auth_admin_controls)
        <header>
            <div class="section-title">{{ __('app.apps.add_tag') }}</div>
            <div class="module-actions">
                <button type="submit"class="button"><i class="fa fa-save"></i><span>{{ __('app.buttons.save') }}</span></button>
                <a href="{{ route('tags.index', []) }}" class="button"><i class="fa fa-ban"></i><span>{{ __('app.buttons.cancel') }}</span></a>
            </div>
        </header>
        <div id="create" class="create">
            {!! csrf_field() !!}

            <div class="input">
                <label>{{ __('app.apps.tag_name') }} *</label>
                {{ html()->text('title')->placeholder(__('app.apps.title'))->class('form-control')->required() }}
                <hr />
                <label>{{ __('app.apps.pinned') }}</label>
                {{ html()->hidden('pinned', '0') }}
                <label class="switch">
                    <?php
                    $checked = true;
                    if(isset($item->pinned) && (bool)$item->pinned !== true) $checked = false;
                    $set_checked = ($checked) ? ' checked="checked"' : '';
                    ?>                   
                    <input type="checkbox" name="pinned" value="1"<?php echo $set_checked;?> />
                    <span class="slider round"></span>
                </label>
            </div>
            <div class="input">
                <label>{{ __('app.apps.colour') }}</label>
                {{ html()->text('colour')->placeholder(__('app.apps.hex'))->class('form-control color-picker') }}
                <hr />
            </div>
            @if($app['config']->get('app.auth_roles_enable', false))
            <div class="input">
                <label>{{ __('app.role') }}</label>
                {{ html()->text('role', $item->role ?? null)->placeholder(__('app.role'))->id('role')->class('form-control') }}
                <hr />
            </div>
            @endif
            <div class="input">
                <label>{{ __('app.apps.icon') }}</label>
                <div class="icon-container">
                    <div id="appimage">
                    @if(isset($item->icon) && !empty($item->icon) || old('icon'))
                    <?php
                        if(isset($item->icon)) $icon = $item->icon;
                        else $icon = old('icon');
                    ?>
                    <img src="{{ asset('storage/'.$icon) }}" />
                    {{ html()->hidden('icon', $icon)->class('form-control') }}
                    @else
                    <img src="/img/heimdall-icon-small.png" />
                    @endif
                    </div>
                    <div class="upload-btn-wrapper">
                        <button class="btn">{{ __('app.buttons.upload')}} </button>
                        <input type="file" id="upload" name="file" />
                    </div>
                </div>
            </div>
            <div class="input">
            <label>{{ __('app.apps.pinned') }}</label>
                {{ html()->hidden('pinned', '0') }}
                <label class="switch">
                    <?php
                    $checked = false;
                    if(isset($item->pinned) && (bool)$item->pinned === true) $checked = true;
                    $set_checked = ($checked) ? ' checked="checked"' : '';
                    ?>                   
                    <input type="checkbox" name="pinned" value="1"<?php echo $set_checked;?> />
                    <span class="slider round"></span>
                </label>
            </div>
            
            <div id="sapconfig"></div>
            
        </div>
        <footer>
            <div class="section-title">&nbsp;</div>
            <div class="module-actions">
                <button type="submit"class="button"><i class="fa fa-save"></i><span>{{ __('app.buttons.save') }}</span></button>
                <a href="{{ route('tags.index', []) }}" class="button"><i class="fa fa-ban"></i><span>{{ __('app.buttons.cancel') }}</span></a>
            </div>
        </footer>
        @else
        <header>
            <div class="section-title">
                {{ __('app.unauthorized_for_form') }}
            </div>
        </header>
        @endif
    </section>
