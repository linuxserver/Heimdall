@extends('layouts.app')

@section('content')

    {!! Form::model($user, ['method' => 'PATCH', 'id' => 'userform', 'files' => true, 'route' => ['users.update', $user->id]]) !!}
    @include('users.form')
    {!! Form::close() !!}

@endsection
@section('scripts')
@endsection