<?php

return [

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
        'scheme' => 'https',
    ],

    'oidc' => [
        'enabled' => env('OIDC_ENABLED', false),
        'issuer' => env('OIDC_ISSUER'),
        'client_id' => env('OIDC_CLIENT_ID'),
        'client_secret' => env('OIDC_CLIENT_SECRET'),
        'redirect_uri' => env('OIDC_REDIRECT_URI'),
        'auto_provision' => env('OIDC_AUTO_PROVISION', true),
        'admin_breakglass_username' => env('OIDC_ADMIN_BREAKGLASS_USERNAME', 'admin'),
        // "src1:dst1,src2:dst2"
        'username_map' => env('OIDC_USERNAME_MAP', ''),
        'scopes' => explode(',', env('OIDC_SCOPES', 'openid,email,profile')),
    ],

];
