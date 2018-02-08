                    <section class="item-container" data-id="{{ $app->id }}">
                        <div class="item" style="background-color: {{ $app->colour }}">
                            @if($app->icon)
                            <img class="app-icon" src="{{ asset('storage/'.$app->icon) }}" />
                            @else
                            <i class="fas fa-app-store-ios"></i>
                            @endif
                            <div class="details">
                                <div class="title">{{ $app->title }}</div>
                                @if(isset($app->config->enabled) && ((bool)$app->config->enabled === true))
                                <div data-id="{{ $app->id }}" class="livestats-container"></div>
                                @endif
                            </div>
                            <a class="link" href="{{ $app->url }}"><i class="fas fa-arrow-alt-to-right"></i></a>
                        </div>
                        <a class="item-edit" href="{{ route('items.edit', $app->id) }}"><i class="fas fa-pencil"></i></a>
                        
                    </section>
