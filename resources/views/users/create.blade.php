@extends('layouts.app')

@section('content')

    {{ html()->form('POST', route('users.store'))->id('userform')->acceptsFiles()->open() }}
    @include('users.form')
    {{ html()->form()->close() }}

@endsection
@section('scripts')
@endsection