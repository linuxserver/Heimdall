<section class="module-container">
@if($enable_auth_admin_controls)

        <header>
            <div class="section-title">
                {{ __($setting->label) }}
                @if($setting->type === 'image')
                @php
                    $max_upload = ini_get('upload_max_filesize');
                    $max_upload_bytes = parse_size($max_upload);
                @endphp
                <a class="settinglink" target="_blank" rel="nofollow noreferer" href="https://github.com/linuxserver/Heimdall?tab=readme-ov-file#new-background-image-not-being-set">({{ format_bytes($max_upload_bytes, false) }})</a>
                @endif

            </div>
            <div class="module-actions">
                <button type="submit"class="button"><i class="fa fa-save"></i><span>{{ __('app.buttons.save') }}</span></button>
                <a href="{{ route('settings.index', []) }}" class="button"><i class="fa fa-ban"></i><span>{{ __('app.buttons.cancel') }}</span></a>
            </div>
        </header>
        <div class="create">
            {!! csrf_field() !!}

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
