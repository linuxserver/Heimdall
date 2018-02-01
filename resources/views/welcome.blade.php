@extends('app')

@section('content')
    @if($apps->first())
        @foreach($apps as $app)
            @include('item')
        @endforeach
        @include('add')
    @else
        There are currently no Applications, add one here
        @include('add')
    @endif


@endsection