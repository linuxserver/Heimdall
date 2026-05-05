@extends('layouts.app')


@section('content')
@if(!$app['config']->get('app.auth_roles_enable', false))
<?php
$user = \App\User::currentUser();
?>
<form class="form-horizontal" method="POST" action="{{ route('login') }}">
    {{ csrf_field() }}
    @if(config('services.oidc.enabled'))
    <div class="oidc-login" style="margin-bottom: 1.5rem; text-align: center;">
        <a href="{{ route('oidc.login') }}" style="display:inline-block; padding:.75rem 1.5rem; background:#fd4b2d; color:#fff; text-decoration:none; border-radius:4px; font-size:1rem;">
            Sign in with Authentik
        </a>
        <div style="margin-top:1.25rem; border-top:1px solid #333; padding-top:1rem; font-size:.8rem; color:#666; letter-spacing:.05em; text-transform:uppercase;">
            Admin local login
        </div>
    </div>
    @endif
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
