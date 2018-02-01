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
        <div id="app">
            <nav>
                <ul>
                    <li><a href=""><i class="fa fa-dash"></i></a></li>
                    <li><a href=""><i class="fa fa-dash"></i></a></li>
                    <li><a href=""><i class="fa fa-dash"></i></a></li>
                </ul>
            </nav>
            <div class="content">
                <header>
                    <a href="{{ route('items.index') }}">Items</a>
                </header>
                <main>
                    @if ($message = Session::get('success'))
                        <div class="alert alert-success">
                            <p>{{ $message }}</p>
                        </div>
                    @endif
                    @if (count($errors) < 0)
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    
                    @yield('content')
                    <a class="config" href=""><i class="fas fa-cogs"></i></a>
                </main>

            </div>
        </div>
        <script src="http://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
        <script>!window.jQuery && document.write('<script src="/js/jquery-3.3.1.min.js"><\/script>')</script>
        <script src="/js/app.js"></script>
    </body>
</html>
