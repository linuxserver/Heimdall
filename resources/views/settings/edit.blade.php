@extends('layouts.app')

@section('content')

    {{ html()->modelForm($setting, 'PATCH', route('settings.edit', $setting->id))->acceptsFiles()->open() }}
    @include('settings.form')
    {{ html()->closeModelForm() }}

@endsection