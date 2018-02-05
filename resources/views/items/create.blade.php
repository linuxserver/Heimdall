@extends('app')

@section('content')

    {!! Form::open(array('route' => 'items.store', 'files' => true, 'method'=>'POST')) !!}
    @include('items.form')
    {!! Form::close() !!}

@endsection
@section('scripts')
    @include('items.scripts')
@endsection