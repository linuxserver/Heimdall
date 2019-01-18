<?php namespace App;

use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;
use App\Item;
use App\Setting;
use Form;
use Cache;

abstract class Search
{

    public static function providers()
    {
        $providers = self::standardProviders();
        $providers = $providers + self::appProviders();
        // Need something to add in none standard providers
        //die(print_r($providers));
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
                'type' => 'external',
            ],
            'ddg' => [
                'url' => 'https://duckduckgo.com/',
                'var' => 'q',
                'method' => 'get',
                'type' => 'external',
            ],
            'bing' => [
                'url' => 'https://www.bing.com/search',
                'var' => 'q',
                'method' => 'get',
                'type' => 'external',
            ],
        ];
    }

    public static function appProviders()
    {
        $providers = [];
        $userapps = Item::all();
        foreach($userapps as $app) {
            if(empty($app->class)) continue;
            if(($provider = Item::isSearchProvider($app->class)) !== false) {
                $name = Item::nameFromClass($app->class);
                $providers[strtolower($name)] = [
                    'type' => $provider->type,
                ];

            }
        }
        return $providers;
    }

    public static function storeSearchProvider($class, $app)
    {
        if(!empty($class)) {
            if(($provider = Item::isSearchProvider($class)) !== false) {
                $providers = Cache::get('search_providers', []);
                $name = Item::nameFromClass($class);

                $search = new $class;

                $providers[strtolower($name)] = [
                    'url' => '',
                    'var' => '',
                    'type' => $search->type,
                    
                ];
            }
        }
    }


        /**
     * @return html
     */
    public static function form()
    {
        $output = '';
        $homepage_search = Setting::fetch('homepage_search');
        $search_provider = Setting::where('key', '=', 'search_provider')->first();
        $user_search_provider = Setting::fetch('search_provider');
        //die(print_r($search_provider));
       
        //die(var_dump($user_search_provider));
        // return early if search isn't applicable
        if((bool)$homepage_search !== true) return $output;
        if($user_search_provider === 'none') return $output;
        if(empty($user_search_provider)) return $output;
        if(is_null($user_search_provider)) return $output;


        if((bool)$homepage_search && (bool)$search_provider) {

            if((bool)$user_search_provider) {
                $name = 'app.options.'.$user_search_provider;
                $provider = self::providerDetails($user_search_provider);

                $output .= '<div class="searchform">';
                $output .= Form::open(['url' => 'search', 'method' => 'get']);
                $output .= '<div id="search-container" class="input-container">';
                $output .= '<select name="provider">';
                foreach(self::providers() as $key => $searchprovider) {
                    $selected = ($key === $user_search_provider) ? ' selected="selected"' : '';
                    $output .= '<option value="'.$key.'"'.$selected.'>'.__('app.options.'.$key).'</option>';
                }
                $output .= '</select>';
                $output .= Form::text('q', null, ['class' => 'homesearch', 'autofocus' => 'autofocus', 'placeholder' => __('app.settings.search').'...']);
                $output .= '<button type="submit">'.ucwords(__('app.settings.search')).'</button>';
                $output .= '</div>';
                $output .= Form::close();
                $output .= '</div>';
            }
        }
        return $output;
    }


}