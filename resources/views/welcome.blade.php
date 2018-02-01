@extends('app')

@section('content')
    @if($apps->first())
        @foreach($apps as $app)
                    <section class="item" style="background-color: {{ $app->colour }}">
                        @if($app->icon)
                        <img src="" />
                        @else
                        <i class="fas fa-app-store-ios"></i>
                        @endif
                        {{ $app->title }}
                        Item
                        <a class="link" href="{{ $app->url }}"><i class="fas fa-arrow-alt-to-right"></i></a>
                    </section>
        @endforeach
    @else
        There are currently no Applications, add one here
    @endif


@endsection