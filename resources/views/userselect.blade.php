@extends('layouts.users')

@section('content')

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

@endsection