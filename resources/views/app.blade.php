<!doctype html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ config('app.name') }}</title>
        <link rel="apple-touch-icon" sizes="57x57" href="/apple-icon-57x57.png">
        <link rel="apple-touch-icon" sizes="60x60" href="/apple-icon-60x60.png">
        <link rel="apple-touch-icon" sizes="72x72" href="/apple-icon-72x72.png">
        <link rel="apple-touch-icon" sizes="76x76" href="/apple-icon-76x76.png">
        <link rel="apple-touch-icon" sizes="114x114" href="/apple-icon-114x114.png">
        <link rel="apple-touch-icon" sizes="120x120" href="/apple-icon-120x120.png">
        <link rel="apple-touch-icon" sizes="144x144" href="/apple-icon-144x144.png">
        <link rel="apple-touch-icon" sizes="152x152" href="/apple-icon-152x152.png">
        <link rel="apple-touch-icon" sizes="180x180" href="/apple-icon-180x180.png">
        <link rel="icon" type="image/png" sizes="192x192"  href="/android-icon-192x192.png">
        <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
        <link rel="icon" type="image/png" sizes="96x96" href="/favicon-96x96.png">
        <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
        <link rel="manifest" href="/manifest.json">
        <meta name="msapplication-TileColor" content="#ffffff">
        <meta name="msapplication-TileImage" content="/ms-icon-144x144.png">
        <meta name="theme-color" content="#ffffff">
        <meta name="mobile-web-app-capable" content="yes">
        <meta name="apple-mobile-web-app-capable" content="yes">
        <link rel="stylesheet" href="{{ mix('css/app.css') }}" type="text/css" />

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
                    ?>
                    <li>{{ $app->title }}<a class="{{ $active }}" data-id="{{ $app->id }}" href="{{ route('items.pintoggle', [$app->id], false) }}"><i class="fas fa-thumbtack"></i></a></li>
                    
                    @endforeach
                </ul>
                @endif
            </nav>
            <div class="content">
                <header class="appheader">
                    <ul>
                        <li><a href="{{ route('dash', [], false) }}">Dash</a></li><li>
                            <a href="{{ route('items.index', [], false) }}">Items</a></li>
                    </ul>
                </header>
                <main>
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
                    
                    @yield('content')
                    <div id="config-buttons">
                        @if(Route::is('dash') || Route::is('tags.show'))
                        <a id="config-button" class="config" href=""><i class="fas fa-exchange"></i></a>
                        @endif
    
                        <a id="dash" class="config" href="{{ route('dash', [], false) }}"><i class="fas fa-th"></i></a>
                        <a id="items" class="config" href="{{ route('items.index', [], false) }}"><i class="fas fa-list"></i></a>
                        <a id="folder" class="config" href="{{ route('tags.index', [], false) }}"><i class="fas fa-tag"></i></a>
                        <a id="settings" class="config" href="{{ route('settings.index', [], false) }}"><i class="fas fa-cogs"></i></a>
                    </div>
                </main>

            </div>
        </div>
        <script src="//ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
        <script>!window.jQuery && document.write('<script src="/js/jquery-3.3.1.min.js"><\/script>')</script>
        <script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
        <script src="/js/app.js?v=2"></script>
        @if ($trianglify == 'true')
        <script src="/js/trianglify.min.js"></script>
        <script>
            function addTriangleTo(target) {
               var dimensions = target.getClientRects()[0];
               var pattern = Trianglify({
                  width: dimensions.width,
                  height: dimensions.height
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
        
    </body>
</html>
