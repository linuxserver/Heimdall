
@if($show_tag_line)
    <header class="tagline" style="background: rgba(0,0,0,.4);
    height: 58px;
    position: revert;
    text-align: center;
    transition: all .35s ease-in-out;
    width: 100%;
    z-index: 1;">
        <ul>
            <li>
                <a   href="/">Dash</a>
            </li>

            @foreach($pin_tags as $tag)
                <li>
                    <a   href="{{ $tag->link }}">{{ $tag->title }}</a>
                </li>
            @endforeach
        </ul>
    </header>
@endif
