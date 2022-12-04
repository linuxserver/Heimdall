@extends('layouts.app')

@section('content')

    @foreach ($groups as $index => $group)
        <section class="module-container">
            <header>
                <div class="section-title">
                    {{ __($group->title) }}
                </div>
                @if($index === 0)
                <div class="module-actions">
                    <a href="{{ route('items.import', []) }}" id="item-import" class="button"><i class="fas fa-file-arrow-up"></i><span>{{ __('import') }}</span></a>
                    <a href="#export" id="item-export" class="button"><i class="fas fa-file-arrow-down"></i><span>{{ __('export') }}</span></a>
                </div>
                @endif
            </header>

            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>{{ __('app.settings.label') }}</th>
                        <th style="width: 60%;">{{ __('app.settings.value') }}</th>
                        <th class="text-center" style="width: 75px;">{{ __('app.settings.edit') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @if (count($group->settings) > 0)
                        @foreach ($group->settings as $setting)
                            <tr>
                                <td>{{ __($setting->label) }}</td>
                                <td>
                                    @if($setting->type === "textarea")
                                    <pre>{{ $setting->list_value }}</pre>
                                    @else
                                    {!! $setting->list_value !!}
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if((bool)$setting->system !== true)
                                    <a href="{!! route('settings.edit', ['id' => $setting->id]) !!}" title="{{ __('app.settings.edit') }} {!! $setting->label !!}" class="secondary"><i class="fa fa-pencil"></i></a>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    @else

                        <tr>
                            <td colspan="3" class="form-error text-center">
                                <strong>{{ __('app.settings.no_items') }}</strong>
                            </td>
                        </tr>
                    @endif

                
                </tbody>
            </table>
        </section>
    @endforeach

@endsection
