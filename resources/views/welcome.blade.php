@extends('app')

@section('content')
    @if($apps->first())
        @foreach($apps as $app)
                    <section class="item" style="background-color: {{ $app->colour }}">
                        {{ $app->title }}
                        Item
                    </section>
        @endforeach
    @else
        There are currently no Applications, add one here
    @endif


@endsection