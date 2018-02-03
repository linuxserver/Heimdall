@extends('app')

@section('content')
    @if($apps->first())
        @include('sortable')        
    @else
        There are currently no Applications, <a href="{{ route('items.create') }}">add one here</a>
        @include('add')
    @endif


@endsection