@extends('layouts.app')

@section('content')

    <section class="module-container">
        @if($enable_auth_admin_controles)
        <header>
            <div class="section-title">{{ __('app.import') }}</div>
            <div class="module-actions">
                <button type="submit" class="button import-button"><i class="fa fa-save"></i><span>{{ __('import') }}</span></button>
                <a href="{{ route('settings.index', []) }}" class="button"><i class="fa fa-ban"></i><span>{{ __('app.buttons.cancel') }}</span></a>
            </div>
        </header>
        <div class="create">
            {!! csrf_field() !!}

            <div class="input">
                <input class="form-control" name="import" type="file">
            </div>
            <div>

                <ul class="import-status" style="display: block">
                </ul>
            </div>


        </div>
        <footer>
            <div class="section-title">&nbsp;</div>
            <div class="module-actions">
                <button type="submit" class="button import-button"><i class="fa fa-save"></i><span>{{ __('import') }}</span></button>
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


@endsection