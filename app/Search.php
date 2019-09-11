<?php namespace App;

use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;
use App\Item;
use App\Setting;
use Form;
use Cache;

abstract class Search
{

    /**
     * List of all search providers
     * 
     * @return Array
     */
    public static function providers()
    {
        $providers = self::standardProviders();
        $providers = $providers + self::appProviders();
        return $providers;
    }

    /**
     * Gets details for a single provider
     * 
     * @return Object
     */
    public static function providerDetails($provider)
    {
        $providers = self::providers();
        if(!isset($providers[$provider])) return false;
        return (object)$providers[$provider] ?? false;
    }

    /**
     * Array of the standard providers
     * 
     * @return Array
     */
    public static function standardProviders()
    {
        return [
            'google' => [
                'url' => 'https://www.google.com/search',
                'var' => 'q',
                'method' => 'get',
                'type' => 'standard',
            ],
            'ddg' => [
                'url' => 'https://duckduckgo.com/',
                'var' => 'q',
                'method' => 'get',
                'type' => 'standard',
            ],
            'bing' => [
                'url' => 'https://www.bing.com/search',
                'var' => 'q',
                'method' => 'get',
                'type' => 'standard',
            ],
            'qwant' => [
                'url' => 'https://www.qwant.com/',
                'var' => 'q',
                'method' => 'get',
                'type' => 'standard',
            ],
            'startpage' => [
                'url' => 'https://www.startpage.com/do/dsearch',
                'var' => 'query',
                'method' => 'get',
                'type' => 'standard',
            ],
        ];
    }

    /**
     * Loops through users apps to see if app is a search provider, might be worth
     * looking into caching this at some point
     * 
     * @return Array
     */
    public static function appProviders()
    {
        $providers = [];
        $userapps = Item::all();
        foreach($userapps as $app) {
            if(empty($app->class)) continue;
            if(($provider = Item::isSearchProvider($app->class)) !== false) {
                $name = Item::nameFromClass($app->class);
                $providers[$app->id] = [
                    'type' => $provider->type,
                    'class' => $app->class,
                    'url' => $app->url,
                    'title' => $app->title,
                    'colour' => $app->colour,
                    'icon' => $app->icon,
                    'description' => $app->description
                ];

            }
        }
        return $providers;
    }

    /**
     * Outputs the search form
     * 
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
        $user_search_provider = $user_search_provider ?? 'none';

        if((bool)$homepage_search && (bool)$search_provider) {

            if((bool)$user_search_provider) {
                $name = 'app.options.'.$user_search_provider;
                $provider = self::providerDetails($user_search_provider);

                $output .= '<div class="searchform">';
                $output .= '<form action="'.url('search').'"'.getLinkTargetAttribute().' method="get">';
                $output .= '<div id="search-container" class="input-container">';
                $output .= '<select name="provider">';
                foreach(self::providers() as $key => $searchprovider) {
                    $selected = ($key === $user_search_provider) ? ' selected="selected"' : '';
                    if (is_numeric($key)) {
                      $output .= '<option value="'.$key.'"'.$selected.'>'.$searchprovider['title'].'</option>';
                    } else {
                      $output .= '<option value="'.$key.'"'.$selected.'>'.__('app.options.'.$key).'</option>';
                    }
                }
                $output .= '</select>';
                $output .= Form::text('q', null, ['class' => 'homesearch', 'autofocus' => 'autofocus', 'placeholder' => __('app.settings.search').'...']);
                $output .= '<button type="submit">'.ucwords(__('app.settings.search')).'</button>';
                $output .= '</div>';
                $output .= '</form>';
                $output .= '</div>';
            }
        }
        return $output;
    }


}
