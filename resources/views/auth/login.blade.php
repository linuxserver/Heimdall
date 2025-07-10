@extends('layouts.app')


@section('content')
@if(!$app['config']->get('app.auth_roles_enable', false))
<?php
$user = \App\User::currentUser();
?>
<form class="form-horizontal" method="POST" action="{{ route('login') }}">
    {{ csrf_field() }}
    <div class="userlist">
    
        <div class="user" href="{{ route('user.set', [$user->id]) }}">
            @if($user->avatar)
            <img class="user-img" src="{{ asset('/storage/'.$user->avatar) }}" />
            @else
            <img class="user-img" src="{{ asset('/img/heimdall-icon-small.png') }}" />
            @endif
            {{ $user->username }}
            <input id="password" type="password" class="form-control" name="password" autofocus required>
            <button type="submit" class="btn btn-primary">Login</button>
        </div>
    </div>
        
</form>
@else
<section class="module-container">
<header>
    <div class="section-title">
        {{ __('app.disabled_feature') }}
    </div>
</header>
</section>
@endif

@endsection
