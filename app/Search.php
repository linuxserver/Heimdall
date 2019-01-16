<?php namespace App;

use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;
use App\Item;

abstract class Search
{

    public static function providers()
    {
        $providers = self::standardProviders();
        // Need something to add in none standard providers

        return $providers;
    }

    public static function providerDetails($provider)
    {
        $providers = self::providers();
        return (object)$providers[$provider] ?? false;
    }

    public static function standardProviders()
    {
        return [
            'google' => [
                'url' => 'https://www.google.com/search',
                'var' => 'q',
                'method' => 'get',
            ],
            'ddg' => [
                'url' => 'https://duckduckgo.com/',
                'var' => 'q',
                'method' => 'get',
            ],
            'bing' => [
                'url' => 'https://www.bing.com/search',
                'var' => 'q',
                'method' => 'get',
            ],
        ];
    }

    public static function storeSearchProvider($class, $app)
    {
        if(!empty($class)) {
            if(($provider = Item::isSearchProvider($class)) !== false) {
                $providers = Cache::get('search_providers', []);
                $name = Item::nameFromClass($class);

                $search = new $class;

                $providers[strtolower($name)] = [
                    'method' => $search->method,
                ];
            }
        }
    }

}