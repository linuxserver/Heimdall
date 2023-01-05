@extends('layouts.app')

@section('content')
        <section class="module-container">
            <header>
                <div class="section-title">
                    {{ __('app.apps.app_list') }}
                    @if( isset($trash) && $trash->count() > 0 )
                        <a class="trashed" href="{{ route('items.index', ['trash' => true]) }}">{{ __('app.apps.view_trash') }} ({{ $trash->count() }})</a>
                    @endif

                </div>
                <div class="module-actions">
                    <a href="{{ route('applist', []) }}" class="button"><i class="fa fa-cloud-download"></i><span>{{ __('app.buttons.downloadapps') }}</span></a>
                    <a href="{{ route('items.create', []) }}" title="" class="button"><i class="fa fa-plus"></i><span>{{ __('app.buttons.add') }}</span></a>
                    <a href="{{ route('dash', []) }}" class="button"><i class="fa fa-ban"></i><span>{{ __('app.buttons.cancel') }}</span></a>
                </div>
            </header>

            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>{{ __('app.title') }}</th>
                        <th>{{ __('app.url') }}</th>
                        <th class="text-center" width="100">{{ __('app.settings.edit') }}</th>
                        <th class="text-center" width="100">{{ __('app.delete') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @if($apps->first())
                        @foreach($apps as $app)
                            <tr>
                                <td>{{ $app->title }}</td>
                                <td><a href="{{ $app->url }}">{{ $app->link }}</a></td>
                                <td class="text-center"><a{{ $app->target }} href="{!! route('items.edit', [$app->id]) !!}" title="{{ __('app.settings.edit') }} {{ $app->title }}"><i class="fas fa-edit"></i></a></td>
                                <td class="text-center">
                                        {!! Form::open(['method' => 'DELETE','route' => ['items.destroy', $app->id],'style'=>'display:inline']) !!}
                                        <button class="link" type="submit"><i class="fa fa-trash-alt"></i></button>
                                        {!! Form::close() !!}
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="4" class="form-error text-center">
                                <strong>{{ __('app.settings.no_items') }}</strong>
                            </td>
                        </tr>
                    @endif

                
                </tbody>
            </table>
        </section>


@endsection