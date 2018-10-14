@extends('layouts.app')

@section('content')

    {!! Form::model($item, ['method' => 'PATCH', 'id' => 'itemform', 'files' => true, 'route' => ['tags.update', $item->id]]) !!}
    @include('tags.form')
    {!! Form::close() !!}

@endsection
@section('scripts')
    @include('tags.scripts')
@endsection