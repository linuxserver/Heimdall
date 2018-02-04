<!doctype html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ config('app.name') }}</title>

        <link rel="stylesheet" href="{{ mix('/css/app.css') }}" type="text/css" />

    </head>
    <body>
        <div id="app"{!! $alt_bg !!}>
            <nav class="sidenav">
                <a class="close-sidenav" href=""><i class="fas fa-times-circle"></i></a>
                @if(isset($all_apps))
                <h2>Pinned Items</h2>
                <ul id="pinlist">
                    @foreach($all_apps as $app)
                    <?php
                    $active = ((bool)$app->pinned === true) ? 'active' : '';
                    ?>
                    <li>{{ $app->title }}<a class="{{ $active }}" data-id="{{ $app->id }}" href="{{ route('items.pintoggle', $app->id) }}"><i class="fas fa-thumbtack"></i></a></li>
                    
                    @endforeach
                </ul>
                @endif
            </nav>
            <div class="content">
                <header class="appheader">
                    <ul>
                        <li><a href="{{ route('dash') }}">Dash</a></li><li>
                            <a href="{{ route('items.index') }}">Items</a></li>
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
                        @if(!Route::is('dash'))
                        <a id="dash" class="config" href="{{ route('dash') }}"><i class="fas fa-th"></i></a>
                        @endif
                        @if(!Request::is(['items', 'items/*']))
                        <a id="items" class="config" href="{{ route('items.index') }}"><i class="fas fa-list"></i></a>
                        @endif
                        @if(!Request::is(['settings', 'settings/*']))
                        <a id="settings" class="config" href="{{ route('settings.index') }}"><i class="fas fa-cogs"></i></a>
                        @endif
                        @if(Route::is('dash'))
                        <a id="config-button" class="config" href=""><i class="fas fa-exchange"></i></a>
                        @endif
                    </div>
                </main>

            </div>
        </div>
        <script src="//ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
        <script>!window.jQuery && document.write('<script src="/js/jquery-3.3.1.min.js"><\/script>')</script>
        <script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
        <script src="/js/app.js"></script>
        
    </body>
</html>
