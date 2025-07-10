@extends('layouts.app')

@section('content')

    {{ html()->form('POST', route('items.store'))->id('itemform')->acceptsFiles()->open() }}
    @include('items.form')
    {{ html()->form()->close() }}

@endsection
@section('scripts')
    @include('items.scripts')
@endsection