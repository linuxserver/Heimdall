@extends('layouts.app')

@section('content')

    {{ html()->modelForm($item, 'PATCH', route('items.update', $item->id))->data('item-id', $item->id)->id('itemform')->acceptsFiles()->open() }}
    @include('items.form')
    {{ html()->closeModelForm() }}

@endsection
@section('scripts')
    @include('items.scripts')
@endsection