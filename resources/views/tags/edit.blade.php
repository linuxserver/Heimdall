@extends('layouts.app')

@section('content')

    {{ html()->modelForm($item, 'PATCH', route('tags.update', $item->id))->id('itemform')->acceptsFiles()->open() }}
    @include('tags.form')
    {{ html()->closeModelForm() }}

@endsection
@section('scripts')
    @include('tags.scripts')
@endsection