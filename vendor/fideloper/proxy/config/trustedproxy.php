<?php

return [

    /*
     * Set trusted proxy IP addresses.
     *
     * Both IPv4 and IPv6 addresses are
     * supported, along with CIDR notation.
     *
     * The "*" character is syntactic sugar
     * within TrustedProxy to trust any proxy
     * that connects directly to your server,
     * a requirement when you cannot know the address
     * of your proxy (e.g. if using ELB or similar).
     *
     */
    'proxies' => null, // [<ip addresses>,], '*', '<ip addresses>,'

    /*
     * To trust one or more specific proxies that connect
     * directly to your server, use an array or a string separated by comma of IP addresses:
     */
    // 'proxies' => ['192.168.1.1'],
    // 'proxies' => '192.168.1.1, 192.168.1.2',

    /*
     * Or, to trust all proxies that connect
     * directly to your server, use a "*"
     */
    // 'proxies' => '*',

    /*
     * Which headers to use to detect proxy related data (For, Host, Proto, Port)
     *
     * Options include:
     *
     * - All headers (see below) - Trust all x-forwarded-* headers
     * - Illuminate\Http\Request::HEADER_FORWARDED - Use the FORWARDED header to establish trust
     * - Illuminate\Http\Request::HEADER_X_FORWARDED_AWS_ELB - If you are using AWS Elastic Load Balancer
     *
     * @link https://symfony.com/doc/current/deployment/proxies.html
     */
    'headers' => Illuminate\Http\Request::HEADER_X_FORWARDED_FOR | Illuminate\Http\Request::HEADER_X_FORWARDED_HOST | Illuminate\Http\Request::HEADER_X_FORWARDED_PORT | Illuminate\Http\Request::HEADER_X_FORWARDED_PROTO | Illuminate\Http\Request::HEADER_X_FORWARDED_AWS_ELB,

];
