@extends('layouts.app')

@section('content')

    <section class="module-container">
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

    </section>


@endsection