                    <section class="item-container" data-id="{{ $app->id }}">
                        <div class="item" style="background-color: {{ $app->colour }}">
                            @if($app->icon)
                            <img class="app-icon" src="{{ asset('storage/'.$app->icon) }}" />
                            @else
                            <i class="fas fa-app-store-ios"></i>
                            @endif
                            {{ $app->title }}
                            
                            <a class="link" href="{{ $app->url }}"><i class="fas fa-arrow-alt-to-right"></i></a>
                        </div>
                    </section>
