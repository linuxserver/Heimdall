@extends('layouts.app')

@section('content')

    {{ html()->modelForm($user, 'PATCH', route('users.update', $user->id))->id('userform')->acceptsFiles()->open() }}
    @include('users.form')
    {{ html()->closeModelForm() }}

@endsection
@section('scripts')
@endsection