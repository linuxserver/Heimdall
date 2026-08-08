<?php

// App-level config files are merged over Laravel's framework defaults key by
// key (see Illuminate\Foundation\Bootstrap\LoadConfiguration), so only the
// keys that need to differ from the default belong here.

return [

    // Framework's default derives this from APP_NAME via Str::snake(), which does
    // not strip characters like "." or spaces. PHP mangles dots in incoming
    // cookie/GET/POST variable names to underscores when parsing a request, so an
    // APP_NAME such as "example.com" produces a cookie the app can never read back
    // (it looks for "example.com_session" but PHP only ever hands it
    // "example_com_session"). That desyncs the session on every request, so the
    // CSRF token embedded in a page never matches on submit -> 419 on every POST.
    // Pinning the cookie name keeps it independent of APP_NAME entirely.
    'cookie' => env('SESSION_COOKIE', 'heimdall_session'),

];
