@extends('app')

@section('content')

    {!! Form::model($item, ['method' => 'PATCH', 'id' => 'itemform', 'files' => true, 'route' => ['users.update', $item->id]]) !!}
    @include('users.form')
    {!! Form::close() !!}

@endsection
@section('scripts')
@endsection