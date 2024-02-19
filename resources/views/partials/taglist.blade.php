<?php
$treat_tags_as = \App\Setting::fetch('treat_tags_as');
?>
@if( $treat_tags_as == 'tags')
    @if($taglist->first())
        <div id="taglist" class="taglist">
            <div class="tag white current" data-tag="all">All</div>
            @foreach($taglist as $tag)
                <div class="tag link{{ title_color($tag->colour) }}" style="background-color: {{ $tag->colour }}" data-tag="tag-{{ $tag->tag_url }}">{{ $tag->title }}</div>
            @endforeach
        </div>
    @endif
@endif
