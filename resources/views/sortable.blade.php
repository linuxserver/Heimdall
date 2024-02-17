    <div id="sortable" class="{{ $treat_tags_as }}">
        @if($treat_tags_as == 'categories')

            @foreach($categories as $category)
                <?php $apps = $category->children; ?>
                <div class="category">
                <div class="title">{{ $category->title }}</div>
                @foreach($apps as $app)
                    @include('item')
                @endforeach
                </div>
            @endforeach


        @else

            @foreach($apps as $app)
                @include('item')
            @endforeach
        @endif

        @include('add')
    </div>
