    <section class="module-container">
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
                {!! Form::text('username', null, array('placeholder' => __('app.user.username'), 'id' => 'appname', 'class' => 'form-control')) !!}
                <hr />
            </div>
            <div class="input">
                <label>{{ __('app.user.email') }} *</label>
                {!! Form::text('email', null, array('placeholder' => 'email@test.com','class' => 'form-control')) !!}
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
                    {!! Form::hidden('avatar', $avatar, ['class' => 'form-control']) !!}
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
                {!! Form::password('password', null, array('class' => 'form-control')) !!}
                <hr />

            </div>
            <div class="input">
                <label>{{ __('app.user.password_confirm') }} *</label>
                {!! Form::password('password_confirmation', null, array('class' => 'form-control')) !!}
            </div>
        </div>

        <div class="input">
                <label>{{ __('app.user.secure_front') }}</label>
                {!! Form::hidden('public_front', '0') !!}
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
                    {!! Form::hidden('autologin_allow', '0') !!}
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

    </section>


