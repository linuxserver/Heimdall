    <section class="module-container">
        <header>
            <div class="section-title">{{ __('app.apps.preview') }}</div>
            <div class="module-actions">
            <div class="toggleinput">
                <label class="name">{{ __('app.apps.pinned') }}</label>
                {!! Form::hidden('pinned', '0') !!}
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
                <button type="submit"class="button"><i class="fa fa-save"></i><span>{{ __('app.buttons.save') }}</span></button>
                <a href="{{ route('items.index', []) }}" class="button"><i class="fa fa-ban"></i><span>{{ __('app.buttons.cancel') }}</span></a>
            </div>
        </header>

        <div id="tile-preview" class="create">
                <div class="">
                @include('items.preview')
                </div>
                <div class="appoptions">
                    <div class="optdetails">
                        <div><button id="optdetails-button" class="dark">{{ __('app.apps.apptype') }}</button></div>
                        <div class="optvalue">
                            <div class="input">
                                {!! Form::select('appid', App\Application::applist(), null, array('class' => 'form-control config-item', 'id' => 'apptype', 'data-config' => 'type')) !!}
                            </div>
                        </div>
                    </div>
                    <div id="searchwebsite" class="optdetails">
                        <div><button class="dark">{{ __('app.apps.website') }}</button></div>
                        <div class="optvalue">
                            <div class="input">
                            {!! Form::text('website', $item->url ?? null, array('placeholder' => __('app.apps.website'), 'id' => 'website', 'class' => 'form-control')) !!}
                            <small class="help">Don't forget http(s)://</small>    
                            </div>
                            <div><button class="btn">Go</button></div>
                        </div>
                    </div>
                </div>
                <div id="websiteiconoptions"></div>
            </div>


            <header style="border-top: 1px solid #dbdce3;">
                <div class="section-title">{{ __('app.apps.add_application') }}</div>
            </header>


            <div id="create" class="create">
            {!! csrf_field() !!}
            <div class="input">
                <label>{{ __('app.apps.application_name') }} *</label>
                {!! Form::text('title', null, array('placeholder' => __('app.apps.title'), 'id' => 'appname', 'class' => 'form-control', 'required')) !!}
            </div>

            <div class="input">
                <label>{{ __('app.apps.colour') }}</label>
                {!! Form::text('colour', $item->colour ?? '#161b1f', array('placeholder' => __('app.apps.hex'), 'id' => 'appcolour', 'class' => 'form-control color-picker set-bg-elem')) !!}
            </div>

            <div class="input">
                <label>{{ strtoupper(__('app.url')) }} *</label>
                {!! Form::text('url', $item->url ?? null, array('placeholder' => __('app.url'), 'id' => 'appurl', 'class' => 'form-control', 'required')) !!}
                <small class="help">Don't forget http(s)://</small>
            </div>

            <div class="input">
                <label>{{ __('app.apps.tags') }} ({{ __('app.optional') }})</label>
                {!! Form::select('tags[]', $tags, $current_tags, ['class' => 'tags', 'multiple']) !!}
            </div>

            <div class="input">
                <div class="icon-container">
                    <div id="appimage">
                    @if(isset($item->icon) && !empty($item->icon) || old('icon'))
                    <?php
                        if(isset($item->icon)) $icon = $item->icon;
                        else $icon = old('icon');
                    ?>
                    <img src="{{ asset('storage/'.$icon) }}" />
                    {!! Form::hidden('icon', $icon, ['class' => 'form-control']) !!}
                    @else
                    <img src="{{ asset('/img/heimdall-icon-small.png') }}" />
                    @endif
                    </div>
                    <div class="upload-btn-wrapper">
                        <button class="btn">{{ __('app.buttons.upload')}} </button>
                        <input type="file" id="upload" name="file" />
                    </div>
                </div>
            </div>
        </div>

        <header style="border-top: 1px solid #dbdce3; width: 100%;">
            <div class="section-title">{{ __('app.apps.description') }}</div>
        </header>
        <div class="create">
            <div class="textarea">
                <textarea name="appdescription" id="appdescription">{{ $item->appdescription ?? '' }}</textarea>
            </div>



            
            @if(isset($item) && $item->enhanced())

            <div id="sapconfig" style="display: block;">
                @if(isset($item))
                @include('SupportedApps::'.App\Item::nameFromClass($item->class).'.config')
                @endif
            </div>

            @elseif(old('class') && App\Item::isEnhanced(old('class')))

            <div id="sapconfig" style="display: block;">
                @include('SupportedApps::'.App\Item::nameFromClass(old('class')).'.config')
            </div>

            @else

            <div id="sapconfig"></div>
            
            @endif
            
        </div>
        <footer>
            <div class="section-title">&nbsp;</div>
            <div class="module-actions">
                <button type="submit"class="button"><i class="fa fa-save"></i><span>{{ __('app.buttons.save') }}</span></button>
                <a href="{{ route('items.index', []) }}" class="button"><i class="fa fa-ban"></i><span>{{ __('app.buttons.cancel') }}</span></a>
            </div>
        </footer>

    </section>


