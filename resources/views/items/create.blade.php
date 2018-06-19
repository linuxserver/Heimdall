@extends('app')

@section('content')

    {!! Form::open(array('route' => 'items.store', 'id' => 'itemform', 'files' => true, 'method'=>'POST')) !!}
    @include('items.form')
    {!! Form::close() !!}

@endsection

@push('scripts')
    @include('items.scripts')
@endpush
