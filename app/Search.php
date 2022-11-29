<?php

namespace App;

use Cache;
use Form;
use Illuminate\Support\Collection;
use Yaml;

abstract class Search
{
    /**
     * List of all search providers
     *
     * @return Collection
     */
    public static function providers(): Collection
    {
        $providers = self::standardProviders();
        $providers = $providers + self::appProviders();

        return collect($providers);
    }

    /**
     * Gets details for a single provider
     *
     * @return false|object
     */
    public static function providerDetails($provider)
    {
        $providers = self::providers();
        if (! isset($providers[$provider])) {
            return false;
        }

        return (object) $providers[$provider] ?? false;
    }

    /**
     * Array of the standard providers
     *
     * @return array
     */
    public static function standardProviders(): array
    {
        // $providers = json_decode(file_get_contents(storage_path('app/searchproviders.json')));
        // print_r($providers);
        $providers = Yaml::parseFile(storage_path('app/searchproviders.yaml'));
        $all = [];
        foreach ($providers as $key => $provider) {
            $all[$key] = $provider;
            $all[$key]['type'] = 'standard';
        }

        return $all;
    }

    /**
     * Loops through users apps to see if app is a search provider, might be worth
     * looking into caching this at some point
     *
     * @return array
     */
    public static function appProviders(): array
    {
        $providers = [];
        $userapps = Item::all();
        foreach ($userapps as $app) {
            if (empty($app->class)) {
                continue;
            }
            if (($provider = Item::isSearchProvider($app->class)) !== false) {
                $name = Item::nameFromClass($app->class);
                $providers[$app->id] = [
                    'id' => $app->id,
                    'type' => $provider->type,
                    'class' => $app->class,
                    'url' => $app->url,
                    'name' => $app->title,
                    'colour' => $app->colour,
                    'icon' => $app->icon,
                    'description' => $app->description,
                ];
            }
        }

        return $providers;
    }

    /**
     * Outputs the search form
     *
     * @return string
     */
    public static function form(): string
    {
        $output = '';
        $homepage_search = Setting::fetch('homepage_search');
        $search_provider = Setting::where('key', '=', 'search_provider')->first();
        $user_search_provider = Setting::fetch('search_provider');
        //die(print_r($search_provider));

        //die(var_dump($user_search_provider));
        // return early if search isn't applicable
        if ((bool) $homepage_search !== true) {
            return $output;
        }
        $user_search_provider = $user_search_provider ?? 'none';

        if ((bool) $search_provider) {
            if ((bool) $user_search_provider) {
                $name = 'app.options.'.$user_search_provider;
                $provider = self::providerDetails($user_search_provider);

                $output .= '<div class="searchform">';
                $output .= '<form action="'.url('search').'"'.getLinkTargetAttribute().' method="get">';
                $output .= '<div id="search-container" class="input-container">';
                $output .= '<select name="provider">';
                foreach (self::providers() as $key => $searchprovider) {
                    $selected = ((string) $key === (string) $user_search_provider) ? ' selected="selected"' : '';
                    $output .= '<option value="'.$key.'"'.$selected.'>'.$searchprovider['name'].'</option>';
                }
                $output .= '</select>';
                $output .= Form::text(
                    'q',
                    null,
                    [
                        'class' => 'homesearch',
                        'autofocus' => 'autofocus',
                        'placeholder' => __('app.settings.search').'...'
                    ]
                );
                $output .= '<button type="submit">'.ucwords(__('app.settings.search')).'</button>';
                $output .= '</div>';
                $output .= '</form>';
                $output .= '</div>';
            }
        }

        return $output;
    }
}
