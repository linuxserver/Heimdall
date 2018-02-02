    <section class="module-container">
        <header>
            <div class="section-title">Add application</div>
            <div class="module-actions">
                <a href="{{ route('items.index') }}" class="button"><i class="fa fa-ban"></i><span>Cancel</span></a>
                <button type="submit"class="button"><i class="fa fa-save"></i><span>Save</span></button>
            </div>
        </header>
        <div class="create">
            {!! csrf_field() !!}
            <!--<div class="input">
                <label>Application name</label>
                {!! Form::select('supported', \App\Item::supportedOptions(), array('placeholder' => 'Title','class' => 'form-control')) !!}
            </div>-->

            <div class="input">
                <label>Application name</label>
                {!! Form::text('title', null, array('placeholder' => 'Title','class' => 'form-control')) !!}
            </div>
            <div class="input">
                <label>Colour</label>
                {!! Form::text('colour', null, array('placeholder' => 'Hex Colour','class' => 'form-control color-picker')) !!}
            </div>
            <div class="input">
                <label>URL</label>
                {!! Form::text('url', null, array('placeholder' => 'Url','class' => 'form-control')) !!}
            </div>
            
            <div class="input">
                <label>Icon *</label>
                @if(isset($item->icon) && !empty($item->icon))
                {{ asset('storage/'.$item->icon) }}
                {!! Form::hidden('icon', $item->icon, ['class' => 'form-control']) !!}
                @endif
                <input name="file" type="file" class="form-control">
                
            </div>


            <label class="switch">
                <?php
                $checked = false;
                if(isset($item->pinned) && $item->pinned === 1) $checked = true;
                $set_checked = ($checked) ? ' checked="checked"' : '';
                ?>
                <input type="checkbox" name="active" value="1"<?php echo $set_checked;?> />
                <span class="slider round"></span>
              </label>

            
        </div>

    </section>
