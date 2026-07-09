<?php

return [

    'default' => env('DB_CONNECTION', 'sqlite'),  // Make sure the default connection is set

    'connections' => [
        'sqlite' => [
            'driver' => 'sqlite',
            // Use the correct path, but let the special in-memory identifier
            // pass through untouched so tests can run against ':memory:'.
            'database' => env('DB_DATABASE', 'app.sqlite') === ':memory:'
                ? ':memory:'
                : database_path(env('DB_DATABASE', 'app.sqlite')),
            'prefix' => '',
            'foreign_key_constraints' => env('DB_FOREIGN_KEYS', true), // Enable foreign key constraints
        ],
    ],


    'migrations' => [
        'table' => 'migrations',
        'update_date_on_publish' => false, // disable to preserve original behavior for existing applications
    ],

];
