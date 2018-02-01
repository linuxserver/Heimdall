@extends('app')

@section('content')
    @if($apps->first())
        @foreach($apps as $app)
            @include('item')
        @endforeach
        @include('add')
    @else
        There are currently no Applications, <a href="{{ route('items.create') }}">add one here</a>
        @include('add')
    @endif


@endsection