<section class="module-container">
@if($enable_auth_admin_controles)
        <header>
            <div class="section-title">{{ __($setting->label) }}</div>
            <div class="module-actions">
                <button type="submit"class="button"><i class="fa fa-save"></i><span>{{ __('app.buttons.save') }}</span></button>
                <a href="{{ route('settings.index', []) }}" class="button"><i class="fa fa-ban"></i><span>{{ __('app.buttons.cancel') }}</span></a>
            </div>
        </header>
        <div class="create">
            {!! csrf_field() !!}
            <?php /*<div class="input">
                <label>Application name</label>
                {!! Form::select('supported', \App\Item::supportedOptions(), array('placeholder' => 'Title','class' => 'form-control')) !!}
            </div>*/ ?>

            <div class="input">
                    {!! $setting->edit_value !!}
            </div>

            
        </div>
        <footer>
            <div class="section-title">&nbsp;</div>
            <div class="module-actions">
                <button type="submit"class="button"><i class="fa fa-save"></i><span>{{ __('app.buttons.save') }}</span></button>
                <a href="{{ route('settings.index', []) }}" class="button"><i class="fa fa-ban"></i><span>{{ __('app.buttons.cancel') }}</span></a>
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
