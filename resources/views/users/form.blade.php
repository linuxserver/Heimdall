    <section class="module-container">
        <header>
            <div class="section-title">{{ __('app.user.add_user') }}</div>
            <div class="module-actions">
                <button type="submit"class="button"><i class="fa fa-save"></i><span>{{ __('app.buttons.save') }}</span></button>
                <a href="{{ route('items.index', [], false) }}" class="button"><i class="fa fa-ban"></i><span>{{ __('app.buttons.cancel') }}</span></a>
            </div>
        </header>
        <div id="create" class="create">
            {!! csrf_field() !!}

            <div class="input">
                <label>{{ __('app.user.name') }} *</label>
                {!! Form::text('name', null, array('placeholder' => __('app.user.name'), 'id' => 'appname', 'class' => 'form-control')) !!}
                <hr />
                <label>{{ __('app.user.name') }} *</label>
                {!! Form::text('title', null, array('placeholder' => __('app.apps.title'), 'id' => 'appname', 'class' => 'form-control')) !!}
                <hr />

            </div>
            <div class="input">
                <label>{{ __('app.user.email') }} *</label>
                {!! Form::text('email', null, array('placeholder' => 'email@test.com','class' => 'form-control')) !!}
                <hr />
                <label>{{ __('app.user.email') }} *</label>
                {!! Form::text('colour', null, array('placeholder' => __('app.apps.hex'),'class' => 'form-control color-picker')) !!}
                <hr />
            </div>
            <div class="input">
            <label>{{ __('app.user.avatar') }}</label>
                <div class="icon-container">
                    <div id="appimage">
                    @if(isset($item->avatar) && !empty($item->avatar) || old('avatar'))
                    <?php
                        if(isset($item->avatar)) $avatar = $item->avatar;
                        else $avatar = old('avatar');
                    ?>
                    <img src="{{ asset('storage/'.$avatar) }}" />
                    {!! Form::hidden('avatar', $avatar, ['class' => 'form-control']) !!}
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

        <div style="margin-top: -40px; width: 100%; padding: 0" class="create">
            <div class="input">
                <label>{{ __('app.user.name') }} *</label>
                {!! Form::text('title', null, array('placeholder' => __('app.apps.title'), 'id' => 'appname', 'class' => 'form-control')) !!}
                <hr />
                <label>{{ __('app.user.secure_front') }}</label>
                {!! Form::hidden('pinned', '0') !!}
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
            <div class="input">
                <label>{{ __('app.apps.colour') }} *</label>
                {!! Form::text('colour', null, array('placeholder' => __('app.apps.hex'),'class' => 'form-control color-picker')) !!}
            </div>
        </div>


            
            @if(isset($item) && isset($item->config->view))
            <div id="sapconfig" style="display: block;">
                @if(isset($item))
                @include('supportedapps.'.$item->config->view)
                @endif
            </div>
            @else
            <div id="sapconfig"></div>
            @endif
            
        </div>
        <footer>
            <div class="section-title">&nbsp;</div>
            <div class="module-actions">
                <button type="submit"class="button"><i class="fa fa-save"></i><span>{{ __('app.buttons.save') }}</span></button>
                <a href="{{ route('items.index', [], false) }}" class="button"><i class="fa fa-ban"></i><span>{{ __('app.buttons.cancel') }}</span></a>
            </div>
        </footer>

    </section>


