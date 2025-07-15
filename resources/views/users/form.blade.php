    <section class="module-container">
        @if($enable_auth_admin_controls)
        <header>
            <div class="section-title">{{ __('app.user.add_user') }}</div>
            <div class="module-actions">
                <button type="submit"class="button"><i class="fa fa-save"></i><span>{{ __('app.buttons.save') }}</span></button>
                <a href="{{ route('users.index', []) }}" class="button"><i class="fa fa-ban"></i><span>{{ __('app.buttons.cancel') }}</span></a>
            </div>
        </header>
        <div id="create" class="create">
            {!! csrf_field() !!}

            <div class="input">
                <label>{{ __('app.user.username') }} *</label>
                {{ html()->text('username')->placeholder(__('app.user.username'))->id('appname')->class('form-control') }}
                <hr />
            </div>
            <div class="input">
                <label>{{ __('app.user.email') }} *</label>
                {{ html()->text('email')->placeholder('email@test.com')->class('form-control') }}
                <hr />
            </div>
            <div class="input">
            <label>{{ __('app.user.avatar') }}</label>
                <div class="icon-container">
                    <div id="appimage">
                    @if(isset($user->avatar) && !empty($user->avatar) || old('avatar'))
                    <?php
                        if(isset($user->avatar)) $avatar = $user->avatar;
                        else $avatar = old('avatar');
                    ?>
                    <img style="max-width: 115px" src="{{ asset('storage/'.$avatar) }}" />
                    {{ html()->hidden('avatar', $avatar)->class('form-control') }}
                    @else
                    <img style="max-width: 115px" src="/img/heimdall-icon-small.png" />
                    @endif
                    </div>
                    <div class="upload-btn-wrapper">
                        <button class="btn">{{ __('app.buttons.upload')}} </button>
                        <input type="file" id="upload" name="file" />
                    </div>
                </div>
            </div>

        <div style="margin-top: -40px; width: 100%; padding: 0" class="create">
            <div class="input">
                <label>{{ __('app.apps.password') }} *</label>
                {{ html()->password('password', array('class' => 'form-control'))->attributes([]) }}
                <hr />

            </div>
            <div class="input">
                <label>{{ __('app.user.password_confirm') }} *</label>
                {{ html()->password('password_confirmation', array('class' => 'form-control'))->attributes([]) }}
            </div>
        </div>

        <div class="input">
                <label>{{ __('app.user.secure_front') }}</label>
                {{ html()->hidden('public_front', '0') }}
                <label class="switch">
                    <?php
                    $checked = true;
                    if(isset($user->public_front) && (bool)$user->public_front === false) $checked = false;
                    $set_checked = ($checked) ? ' checked="checked"' : '';
                    ?>                   
                    <input type="checkbox" name="public_front" value="1"<?php echo $set_checked;?> />
                    <span class="slider round"></span>
                </label>
            </div>
            <div class="input">
                    <label>{{ __('app.user.autologin') }}</label>
                    {{ html()->hidden('autologin_allow', '0') }}
                    <label class="switch">
                        <?php
                        $checked = false;
                        if(isset($user->autologin) && !empty($user->autologin)) $checked = true;
                        $set_checked = ($checked) ? ' checked="checked"' : '';
                        ?>                   
                        <input type="checkbox" name="autologin_allow" value="1"<?php echo $set_checked;?> />
                        <span class="slider round"></span>
                    </label>
                    
                </div>
    
                        
        </div>
        <footer>
            <div class="section-title">&nbsp;</div>
            <div class="module-actions">
                <button type="submit"class="button"><i class="fa fa-save"></i><span>{{ __('app.buttons.save') }}</span></button>
                <a href="{{ route('users.index', []) }}" class="button"><i class="fa fa-ban"></i><span>{{ __('app.buttons.cancel') }}</span></a>
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
