                    <?php
                    $item = $item ?? new App\Item;
                    ?>
                    <section class="item-container" data-id="">
                        <div class="item set-bg-elem" style="background-color: {{ $item->colour ?? '#222' }}">
                            <div class="app-icon-container">
                                @if(isset($item->icon) && !empty($item->icon))
                                <img class="app-icon" src="{{ asset('/storage/'.$item->icon) }}" />
                                @else
                                <img class="app-icon" src="{{ asset('/img/heimdall-icon-small.png') }}" />
                                @endif
                            </div>
                            <div class="details">
                                <div class="title{{ title_color($item->colour) ?? 'white' }}">{{ $item->title ?? '' }}</div>
                                @if($item->enhanced())
                                <div data-id="{{ $item->id }}" data-dataonly="{{ $item->getconfig()->dataonly ?? '0' }}" class="no-livestats-container"></div>
                                @endif
                            </div>
                            <a class="link{{ title_color($item->colour) }}"{!! $item->link_target !!} href="{{ $item->link }}"><i class="fas {{ $item->link_icon }}"></i></a>
                        </div>
                        @if(isset($item->id))
                        <a class="item-edit" href="{{ route($item->link_type.'.edit', [ $item->id ]) }}"><i class="fas fa-pencil"></i></a>
                        @endif
                    </section>
