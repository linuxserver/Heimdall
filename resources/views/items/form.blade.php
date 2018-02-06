    <section class="module-container">
        <header>
            <div class="section-title">Add application</div>
            <div class="module-actions">
                <button type="submit"class="button"><i class="fa fa-save"></i><span>Save</span></button>
                <a href="{{ route('items.index') }}" class="button"><i class="fa fa-ban"></i><span>Cancel</span></a>
            </div>
        </header>
        <div class="create">
            {!! csrf_field() !!}
            <!--<div class="input">
                <label>Application name</label>
                {!! Form::select('supported', \App\Item::supportedOptions(), array('placeholder' => 'Title','class' => 'form-control')) !!}
            </div>-->

            <div class="input">
                <label>Application name *</label>
                {!! Form::text('title', null, array('placeholder' => 'Title', 'id' => 'appname', 'class' => 'form-control')) !!}
                <hr />
                <label>URL</label>
                {!! Form::text('url', null, array('placeholder' => 'Url','class' => 'form-control')) !!}
            </div>
            <div class="input">
                <label>Colour *</label>
                {!! Form::text('colour', null, array('placeholder' => 'Hex Colour','class' => 'form-control color-picker')) !!}
                <hr />
                <label>Pinned</label>
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
                <label>Icon</label>
                <div class="icon-container">
                    <div id="appimage">
                    @if(isset($item->icon) && !empty($item->icon))
                    <img src="{{ asset('storage/'.$item->icon) }}" />
                    {!! Form::hidden('icon', $item->icon, ['class' => 'form-control']) !!}
                    @endif
                    </div>
                    <div class="upload-btn-wrapper">
                        <button class="btn">Upload a file</button>
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
                <button type="submit"class="button"><i class="fa fa-save"></i><span>Save</span></button>
                <a href="{{ route('items.index') }}" class="button"><i class="fa fa-ban"></i><span>Cancel</span></a>
            </div>
        </footer>

    </section>


