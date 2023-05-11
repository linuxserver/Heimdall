@extends('layouts.app')

@section('content')

    {!! Form::open(array('route' => 'tags.store', 'id' => 'itemform', 'files' => true, 'method'=>'POST')) !!}
    @include('tags.form')
    {!! Form::close() !!}

@endsection
@section('scripts')
    @include('tags.scripts')
@endsection