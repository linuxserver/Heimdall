@extends('layouts.app')

@section('content')

    {!! Form::open(array('route' => 'users.store', 'id' => 'itemform', 'files' => true, 'method'=>'POST')) !!}
    @include('users.form')
    {!! Form::close() !!}

@endsection
@section('scripts')
@endsection