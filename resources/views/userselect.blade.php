@extends('layouts.users')

@section('content')
@if(!$app['config']->get('app.auth_roles_enable', false))
<div class="userlist">
@foreach($users as $user)
    <a class="user" href="{{ route('user.set', [$user->id]) }}">
        @if($user->avatar)
        <img class="user-img" src="{{ asset('/storage/'.$user->avatar) }}" />
        @else
        <img class="user-img" src="{{ asset('/img/heimdall-icon-small.png') }}" />
        @endif
        {{ $user->username }}
    </a>
@endforeach
</div>
@else
<section class="module-container">
<header>
    <div class="section-title">
        {{ __('app.diabled_feature') }}
    </div>
</header>
</section>
@endif

@endsection