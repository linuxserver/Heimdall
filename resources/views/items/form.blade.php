    <section class="module-container">
        <header>
            <div class="section-title">{{ __('app.apps.add_application') }}</div>
            <div class="module-actions">
                <button type="submit"class="button"><i class="fa fa-save"></i><span>{{ __('app.buttons.save') }}</span></button>
                <a href="{{ route('items.index') }}" class="button"><i class="fa fa-ban"></i><span>{{ __('app.buttons.cancel') }}</span></a>
            </div>
        </header>
        <div class="create">
            {!! csrf_field() !!}
            <!--<div class="input">
                <label>Application name</label>
                {!! Form::select('supported', \App\Item::supportedOptions(), array('placeholder' => 'Title','class' => 'form-control')) !!}
            </div>-->

            <div class="input">
                <label>{{ __('app.apps.application_name') }} *</label>
                {!! Form::text('title', null, array('placeholder' => __('app.apps.title'), 'id' => 'appname', 'class' => 'form-control')) !!}
                <hr />
                <label>{{ strtoupper(__('app.url')) }}</label>
                {!! Form::text('url', null, array('placeholder' => __('app.url'),'class' => 'form-control')) !!}
            </div>
            <div class="input">
                <label>{{ __('app.apps.colour') }} *</label>
                {!! Form::text('colour', null, array('placeholder' => __('app.apps.hex'),'class' => 'form-control color-picker')) !!}
                <hr />
                <label>{{ __('app.apps.pinned') }}</label>
                <label class="switch">
                    <?php
                    $checked = false;
                    if(isset($item->pinned) && (bool)$item->pinned === true) $checked = true;
                    $set_checked = ($checked) ? ' checked="checked"' : '';
                    ?>
                    {!! Form::hidden('pinned', '0') !!}
                    <input type="checkbox" name="pinned" value="1"<?php echo $set_checked;?> />
                    <span class="slider round"></span>
                </label>
            </div>
            <div class="input">
                <label>{{ __('app.apps.icon') }}</label>
                <div class="icon-container">
                    <div id="appimage">
                    @if(isset($item->icon) && !empty($item->icon))
                    <img src="{{ asset('storage/'.$item->icon) }}" />
                    {!! Form::hidden('icon', $item->icon, ['class' => 'form-control']) !!}
                    @endif
                    </div>
                    <div class="upload-btn-wrapper">
                        <button class="btn">{{ __('app.buttons.upload')}} </button>
                        <input type="file" name="myfile" />
                    </div>
                </div>
            </div>
            
            @if(isset($item) && $item->config)
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
                <a href="{{ route('items.index') }}" class="button"><i class="fa fa-ban"></i><span>{{ __('app.buttons.cancel') }}</span></a>
            </div>
        </footer>

    </section>


