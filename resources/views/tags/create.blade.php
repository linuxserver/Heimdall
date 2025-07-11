@extends('layouts.app')

@section('content')

    {{ html()->form('POST', route('tags.store'))->id('itemform')->acceptsFiles()->open() }}
    @include('tags.form')
    {{ html()->form()->close() }}

@endsection
@section('scripts')
    @include('tags.scripts')
@endsection