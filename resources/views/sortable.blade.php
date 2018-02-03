    <div id="sortable">
        @foreach($apps as $app)
            @include('item')
        @endforeach
        @include('add')
    </div>
