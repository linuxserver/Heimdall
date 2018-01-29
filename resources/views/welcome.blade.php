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
                    test
                </header>
                <main>
                    <section class="item">
                        Item
                    </section>
                </main>

                <footer>
                    test
                </footer>
            </div>
        </div>
    </body>
</html>
