@extends('layouts.app')

@section('content')

    {!! Form::model($item, ['method' => 'PATCH', 'id' => 'itemform', 'files' => true, 'route' => ['items.update', $item->id]]) !!}
    @include('items.form')
    {!! Form::close() !!}

@endsection
@section('scripts')
    @include('items.scripts')
@endsection