@extends('app')

@section('content')

    {!! Form::model($item, ['method' => 'PATCH','route' => ['items.update', $item->id]]) !!}
    @include('items.form')
    {!! Form::close() !!}

@endsection