                    <section class="item-container{{ $app->droppable }}" data-id="{{ $app->id }}">
                        <div class="item" style="background-color: {{ $app->colour }}">
                            @if($app->icon)
                            <img class="app-icon" src="/storage/{{ $app->icon }}" />
                            @else
                            <img class="app-icon" src="/img/heimdall-icon-small.png" />
                            @endif
                            <div class="details">
                                <div class="title{{ title_color($app->colour) }}">{{ $app->title }}</div>
                                @if(isset($app->config->enabled) && ((bool)$app->config->enabled === true))
                                <div data-id="{{ $app->id }}" data-dataonly="{{ $app->config->dataonly or '0' }}" class="livestats-container"></div>
                                @endif
                            </div>
                            <a class="link{{ title_color($app->colour) }}"{!! $app->link_target !!} href="{{ $app->link }}"><i class="fas {{ $app->link_icon }}"></i></a>
                        </div>
                        <a class="item-edit" href="{{ route($app->link_type.'.edit', [ $app->id ], false) }}"><i class="fas fa-pencil"></i></a>
                        
                    </section>
