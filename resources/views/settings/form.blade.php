<section class="module-container">
        <header>
            <div class="section-title">{{ $setting->label }}</div>
            <div class="module-actions">
                <button type="submit"class="button"><i class="fa fa-save"></i><span>Save</span></button>
                <a href="{{ route('settings.index') }}" class="button"><i class="fa fa-ban"></i><span>Cancel</span></a>
            </div>
        </header>
        <div class="create">
            {!! csrf_field() !!}
            <!--<div class="input">
                <label>Application name</label>
                {!! Form::select('supported', \App\Item::supportedOptions(), array('placeholder' => 'Title','class' => 'form-control')) !!}
            </div>-->

            <div class="input">
                    @php($type = explode('|', $setting->type)[0])
                    {!! Form::label('value', 'Value') !!}
                    @if ($type == 'image')
                    {!! Form::file('value', ['class' => 'form-control']) !!}
                    @elseif ($type == 'select')
                    @php($options = explode('|', $setting->type)[1])
                    @php($options = explode(',', $options))
                    {!! Form::select('value', $options, null, ['class' => 'form-control']) !!}
                    @elseif ($type == 'textarea')
                    {!! Form::textarea('value', Request::get('value'), ['class' => 'form-control trumbowyg', 'placeholder' => 'FAQ contents']) !!}
                    @else
                    {!! Form::text('value', null, ['class' => 'form-control']) !!}
                    @endif

            </div>

            
        </div>
        <footer>
            <div class="section-title">&nbsp;</div>
            <div class="module-actions">
                <button type="submit"class="button"><i class="fa fa-save"></i><span>Save</span></button>
                <a href="{{ route('settings.index') }}" class="button"><i class="fa fa-ban"></i><span>Cancel</span></a>
            </div>
        </footer>

    </section>
