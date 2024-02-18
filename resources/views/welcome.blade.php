@extends('layouts.app')

@section('content')
    @include('partials.taglist')
    @include('partials.search')

    @if((isset($apps) && $apps->first()) || (isset($categories) && $categories->first()))
        @include('sortable')        
    @else
    <div class="message-container2">
            <div class="alert alert-danger">
                    <p>{!! __('app.dash.no_apps', 
                        [
                            'link1' => '<a href="'.route('items.create', []).'">'.__('app.dash.link1').'</a>', 
                            'link2' => '<a id="pin-item" href="">'.__('app.dash.link2').'</a>'
                        ]) !!}</p>
                    </div>
                    
    </div>
        <div id="sortable">
        @include('add')
        </div>
    @endif


@endsection