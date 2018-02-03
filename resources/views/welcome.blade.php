@extends('app')

@section('content')
    @if($apps->first())
        @include('sortable')        
    @else
    <div class="message-container2">
            <div class="alert alert-danger">
                    <p>There are currently no pinned Applications, <a href="{{ route('items.create') }}">Add an application here</a> or <a id="pin-item" href="">Pin an item to the dash</a></p>
                    </div>
                    
    </div>
        <div id="sortable">
        @include('add')
        </div>
    @endif


@endsection