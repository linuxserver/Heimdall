@extends('layouts.app')

@section('content')
        <section class="module-container">
            <header>
                <div class="section-title">
                    {{ __('app.apps.show_deleted') }}
                </div>
                <div class="module-actions">
                    <a href="{{ route('items.index', []) }}" title="" class="button"><i class="fa fa-ban"></i><span>{{ __('app.buttons.cancel') }}</span></a>
                </div>
            </header>

            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>{{ __('app.title') }}</th>
                        <th>Url</th>
                        <th class="text-center" width="100">{{ __('app.restore') }}</th>
                        <th class="text-center" width="100">{{ __('app.delete') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @if($trash->first())
                        @foreach($trash as $app)
                            <tr>
                                <td>{{ $app->title }}</td>
                                <td>{{ __('app.url') }}</td>
                                <td class="text-center"><a href="{!! route('items.restore', [$app->id]) !!}" title="{{ __('app.restore') }} {!! $app->title !!}"><i class="fas fa-undo"></i></a></td>
                                <td class="text-center">
                                        {!! Form::open(['method' => 'DELETE','route' => ['items.destroy', $app->id],'style'=>'display:inline']) !!}
                                        <input type="hidden" name="force" value="1" />
                                        <button type="submit"><i class="fa fa-trash-alt"></i></button>
                                        {!! Form::close() !!}
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="5" class="form-error text-center">
                                <strong>{{ __('app.settings.no_items') }}</strong>
                            </td>
                        </tr>
                    @endif

                
                </tbody>
            </table>
        </section>


@endsection