                    <section class="item-container{{ $app->droppable }}" data-id="{{ $app->id }}">
                        <div class="item" style="background-color: {{ $app->colour }}">
                            @if($app->icon)
                            <img class="app-icon" src="{{ asset('/storage/'.str_replace('supportedapps', 'icons', $app->icon)) }}" alt="" />
                            @else
                            <img class="app-icon" src="{{ asset('/img/heimdall-icon-small.png') }}" alt="" />
                            @endif
                            <div class="details">
                                <div class="title{{ title_color($app->colour) }}">{{ $app->title }}</div>
                                @if($app->enabled())
                                <div data-id="{{ $app->id }}" data-dataonly="{{ $app->getconfig()->dataonly ?? '0' }}" class="livestats-container{{ title_color($app->colour) }}"></div>
                                @endif
                            </div>
                            <a class="link{{ title_color($app->colour) }}"{!! $app->link_target !!} href="{{ $app->link }}" rel="noopener"><i class="fas {{ $app->link_icon }}"></i></a>
                        </div>
                        <a class="item-edit" href="{{ route($app->link_type.'.edit', [ $app->id ], false) }}"><i class="fas fa-pencil"></i></a>
                        
                    </section>
