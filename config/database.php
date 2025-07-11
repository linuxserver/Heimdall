<?php

return [

    'default' => env('DB_CONNECTION', 'sqlite'),  // Make sure the default connection is set

    'connections' => [
        'sqlite' => [
            'driver' => 'sqlite',
            'database' => database_path(env('DB_DATABASE', 'app.sqlite')), // Make sure to use the correct path
            'prefix' => '',
            'foreign_key_constraints' => env('DB_FOREIGN_KEYS', true), // Enable foreign key constraints
        ],
    ],


    'migrations' => [
        'table' => 'migrations',
        'update_date_on_publish' => false, // disable to preserve original behavior for existing applications
    ],

];
