@extends('layouts.app')

@section('content')
        <section class="module-container">
            <header>
                <div class="section-title">
                    {{ __('app.user.user_list') }}
                    @if( isset($trash) && $trash->count() > 0 )
                        <a class="trashed" href="{{ route('users.index', ['trash' => true]) }}">{{ __('app.apps.view_trash') }} ({{ $trash->count() }})</a>
                    @endif

                </div>
                <div class="module-actions">
                    <a href="{{ route('users.create', []) }}" title="" class="button"><i class="fa fa-plus"></i><span>{{ __('app.buttons.add') }}</span></a>
                    <a href="{{ route('dash', []) }}" class="button"><i class="fa fa-ban"></i><span>{{ __('app.buttons.cancel') }}</span></a>
                </div>
            </header>

            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>{{ __('app.user.username') }}</th>
                        <th>{{ __('app.apps.password') }}</th>
                        <th>{{ __('app.apps.autologin_url') }}</th>
                        <th class="text-center" width="100">{{ __('app.settings.edit') }}</th>
                        <th class="text-center" width="100">{{ __('app.delete') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @if($users->first())
                        @foreach($users as $user)
                            <tr>
                                <td>{{ $user->username }}</td>
                                <td><i class="fa {{ (!is_null($user->password) ? 'fa-check' : 'fa-times') }}" /></td>
                                <td>
                                @if(is_null($user->autologin))
                                    <i class="fa fa-times" />
                                @else
                                    <a href="{{ route('user.autologin', $user->autologin) }}">{{ route('user.autologin', $user->autologin) }}</a>
                                @endif
                                </td>
                                <td class="text-center"><a{{ $user->target }} href="{!! route('users.edit', [$user->id]) !!}" title="{{ __('user.settings.edit') }} {!! $user->title !!}"><i class="fas fa-edit"></i></a></td>
                                <td class="text-center">
                                    @if($user->id !== 1)
                                        {!! Form::open(['method' => 'DELETE','route' => ['users.destroy', $user->id],'style'=>'display:inline']) !!}
                                        <button class="link" type="submit"><i class="fa fa-trash-alt"></i></button>
                                        {!! Form::close() !!}
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="4" class="form-error text-center">
                                <strong>{{ __('app.user.settings.no_items') }}</strong>
                            </td>
                        </tr>
                    @endif

                
                </tbody>
            </table>
        </section>


@endsection