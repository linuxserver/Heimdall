@extends('layouts.users')

@section('content')

@foreach($users as $user)
    <a href="{{ route('user.set', [$user->id]) }}">{{ $user->name }}</a>
@endforeach

@endsection