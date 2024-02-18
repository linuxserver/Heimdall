    <div id="sortable" class="{{ $treat_tags_as ?? '' }}">
        @if(isset($treat_tags_as) && $treat_tags_as == 'categories')

            @foreach($categories as $category)
                <?php $apps = $category->children; ?>
                <div class="category item-container" data-name="{{ $category->title }}" data-id="{{ $category->id }}">
                <div class="title"><a href="{{ $category->link }}" style="{{ $category->colour ? 'color: ' . $category->colour .';' : '' }}">{{ $category->title }}</a></div>
                @foreach($apps as $app)
                    @include('item')
                @endforeach
                </div>
            @endforeach


        @else

            @foreach($apps as $app)
                @include('item')
            @endforeach
            @include('add')
        @endif

        
    </div>
