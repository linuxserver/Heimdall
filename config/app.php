<?php

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Facade;

return [

    'version' => '2.7.5',

    'appsource' => env('APP_SOURCE', 'https://appslist.heimdall.site/'),

    'allow_internal_requests' => env('ALLOW_INTERNAL_REQUESTS', false),

    'aliases' => Facade::defaultAliases()->merge([
        'EnhancedApps' => App\EnhancedApps::class,
        'Form' => App\Facades\Form::class,
        'Redis' => Illuminate\Support\Facades\Redis::class,
        'SupportedApps' => App\SupportedApps::class,
        'Yaml' => Symfony\Component\Yaml\Yaml::class,
    ])->toArray(),

    'auth_roles_enable' =>  (bool) env('AUTH_ROLES_ENABLE', false),

    'auth_roles_header' =>  env('AUTH_ROLES_HEADER', 'remote-groups'),

    'auth_roles_http_header' =>  env('AUTH_ROLES_HTTP_HEADER', 'HTTP_REMOTE_GROUPS'),

    'auth_roles_admin' =>  env('AUTH_ROLES_ADMIN', 'admin'),

    'auth_roles_delimiter' =>  env('AUTH_ROLES_DELIMITER', ','),

];
