<!doctype html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ config('app.name') }}</title>
        <link rel="apple-touch-icon" sizes="57x57" href="{{ asset('apple-icon-57x57.png') }}">
        <link rel="apple-touch-icon" sizes="60x60" href="{{ asset('apple-icon-60x60.png') }}">
        <link rel="apple-touch-icon" sizes="72x72" href="{{ asset('apple-icon-72x72.png') }}">
        <link rel="apple-touch-icon" sizes="76x76" href="{{ asset('apple-icon-76x76.png') }}">
        <link rel="apple-touch-icon" sizes="114x114" href="{{ asset('apple-icon-114x114.png') }}">
        <link rel="apple-touch-icon" sizes="120x120" href="{{ asset('apple-icon-120x120.png') }}">
        <link rel="apple-touch-icon" sizes="144x144" href="{{ asset('apple-icon-144x144.png') }}">
        <link rel="apple-touch-icon" sizes="152x152" href="{{ asset('apple-icon-152x152.png') }}">
        <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('apple-icon-180x180.png') }}">
        <link rel="icon" type="image/png" sizes="192x192"  href="{{ asset('android-icon-192x192.png') }}">
        <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32x32.png') }}">
        <link rel="icon" type="image/png" sizes="96x96" href="{{ asset('favicon-96x96.png') }}">
        <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon-16x16.png') }}">
        <link rel="mask-icon" href="{{ asset('img/heimdall-logo-small.svg') }}" color="black">
        <meta name="msapplication-TileColor" content="#ffffff">
        <meta name="msapplication-TileImage" content="{{ asset('ms-icon-144x144.png') }}">
        <meta name="theme-color" content="#ffffff">
        <meta name="mobile-web-app-capable" content="yes">
        <meta name="apple-mobile-web-app-capable" content="yes">
        <link rel="stylesheet" href="{{ asset(mix('css/app.css')) }}" type="text/css" />
        <link rel="stylesheet" href="{{ asset('css/all.min.css?v='.config('app.version')) }}" type="text/css" />
        <script src="{{ asset('js/fontawesome.js') }}"></script>
        @if(config('app.url') !== 'http://localhost')
        <base href="{{ config('app.url') }}">
        @else
        <base href="{{ url('') }}">
        @endif
        <style id="custom_css">
        /* editable using the 'Settings > Advanced > Custom CSS' option */
        {!! \App\Setting::fetch('custom_css') !!}
        </style>
    </head>
    <body>
        <div id="app"{!! $alt_bg !!}>
            <nav class="sidenav">
                <a class="close-sidenav" href=""><i class="fas fa-times-circle"></i></a>
                @if(isset($all_apps))
                <h2>{{ __('app.dash.pinned_items') }}</h2>
                <ul id="pinlist">
                    @foreach($all_apps as $app)
                    <?php
                    $active = ((bool)$app->pinned === true) ? 'active' : '';
                    if($app->title == 'app.dashboard') continue;
                    ?>
                    <li>{{ $app->title }}<a class="{{ $active }}" data-tag="{{ $tag ?? 0 }}" data-id="{{ $app->id }}" href="{{ route('items.pintoggle', [$app->id]) }}"><i class="fas fa-thumbtack"></i></a></li>
                    
                    @endforeach
                </ul>
                @endif
            </nav>
            <div class="content">
                <header class="appheader">
                    <ul>
                        <li><a href="{{ route('dash', []) }}">Dash</a></li><li>
                            <a href="{{ route('items.index', []) }}">Items</a></li>
                    </ul>
                </header>
                <main id="main">
                    @if ($message = Session::get('success'))
                    <div class="message-container">
                        <div class="alert alert-success">
                            <p>{{ $message }}</p>
                        </div>
                    </div>
                    @endif
                    @if (count($errors) > 0)
                    <div class="message-container">
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                    @endif
                    @if($allusers->count() > 1)
                    <div id="switchuser">
                        @if($current_user->avatar)
                        <img class="user-img" src="{{ asset('/storage/'.$current_user->avatar) }}" />
                        @else
                        <img class="user-img" src="{{ asset('/img/heimdall-icon-small.png') }}" />
                        @endif
                        {{ $current_user->username }}
                        <a class="btn" href="{{ route('user.select') }}">Switch User</a>
                    </div>
                    @endif
                    @yield('content')
                    <div id="config-buttons">

                        
                        @if(Route::is('dash') || Route::is('tags.show'))
                        <a id="config-button" class="config" href=""><i class="fas fa-exchange"></i><div class="tooltip left">{{ __('app.dashboard.reorder') }}</div></a>
                        
                        @endif
    
                        <a id="dash" class="config" href="{{ route('dash', []) }}"><i class="fas fa-th"></i><div class="tooltip left">{{ __('app.dashboard') }}</div></a>
                        @if($current_user->id === 1)
                        <a id="users" class="config" href="{{ route('users.index', []) }}"><i class="fas fa-user"></i><div class="tooltip left">{{ __('app.user.user_list') }}</div></a>
                        @endif
                        <a id="items" class="config" href="{{ route('items.index', []) }}"><i class="fas fa-list"></i><div class="tooltip left">{{ __('app.apps.app_list') }}</div></a>
                        <a id="folder" class="config" href="{{ route('tags.index', []) }}"><i class="fas fa-tag"></i><div class="tooltip left">{{ __('app.apps.tag_list') }}</div></a>
                        <a id="settings" class="config" href="{{ route('settings.index', []) }}"><i class="fas fa-cogs"></i><div class="tooltip left">{{ __('app.dashboard.settings') }}</div></a>
                    </div>
                </main>

            </div>
        </div>
        <script src="{{ asset('js/jquery.min.js') }}"></script>
        <script src="{{ asset(mix('js/app.js')) }}"></script>
        @if($trianglify == 'true')
        <script src="{{ asset('js/trianglify.js') }}"></script>
        <script>
            function addTriangleTo(target) {
               var dimensions = target.getClientRects()[0];
               var pattern = Trianglify({
                  width: dimensions.width,
                  height: dimensions.height
                  @if($trianglify_seed <> '')
                  , seed: '{!! $trianglify_seed !!}'
                  @endif
               });
               target.style['background-image'] = 'url(' + pattern.png() + ')';
               target.style['background-size'] = 'cover';
               target.style['-webkit-background-size'] = 'cover';
               target.style['-moz-background-size'] = 'cover';
               target.style['-o-background-size'] = 'cover';
            }
            var resizeTimer;
            $(window).on('resize', function(e) {
               clearTimeout(resizeTimer);
               resizeTimer = setTimeout(function() {
                  addTriangleTo(app);
               }, 400);
            });
        </script>
        <script>addTriangleTo(app);</script>
        @endif
        @yield('scripts')
        
        <script id="custom_js">
        /* editable using the 'Settings > Advanced > Custom JavaScript' option */
        {!! \App\Setting::fetch('custom_js') !!}
        </script>
    </body>
</html>
