@extends('layouts.app')

@section('content')

    {!! Form::model($setting, ['method' => 'PATCH', 'files' => true, 'route' => ['settings.edit', $setting->id]]) !!}
    @include('settings.form')
    {!! Form::close() !!}

@endsection