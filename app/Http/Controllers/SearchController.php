<?php

namespace App\Http\Controllers;

use App\Item;
use App\Search;
use App\Setting;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Http;

class SearchController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        // The item list exposes the user's apps, so it gets the dashboard gate
        $this->middleware('allowed')->only('items');
    }

    /**
     * @return Application|RedirectResponse|Redirector|mixed|void
     */
    public function index(Request $request)
    {
        $requestprovider = $request->input('provider');
        $query = $request->input('q');

        // Sanitize the query to prevent XSS
        $query = htmlspecialchars($query, ENT_QUOTES, 'UTF-8'); 

        $provider = Search::providerDetails($requestprovider);

        if (!$provider || !isset($provider->type)) {
            abort(404, 'Invalid provider');
        }

        // If the query is empty, redirect to the provider's base URL
        if (!$query || trim($query) === '') {
            return redirect($provider->url);
        }

        if ($provider->type == 'standard') {
            return redirect($provider->url.'?'.$provider->query.'='.urlencode($query));
        } elseif ($provider->type == 'external') {
            $class = new $provider->class;
            return $class->getResults($query, $provider);
        }

        abort(404, 'Provider type not supported');
    }

    /**
     * All apps the current user can see, pinned or not, for the quick launcher
     */
    public function items(Request $request): JsonResponse
    {
        $query = Item::where('type', 0)->with('parents')->orderBy('order', 'asc');

        if (config('app.auth_roles_enable')) {
            $roles = explode(
                config('app.auth_roles_delimiter'),
                $request->header(config('app.auth_roles_header')) ?? ''
            );
            $query->whereIn('role', $roles);
        }

        $items = $query->get()->map(function (Item $app) {
            return [
                'id' => $app->id,
                'title' => $app->title,
                'url' => $app->link,
                'colour' => $app->colour,
                'icon' => $app->icon_url,
                'tags' => $app->parents->where('id', '!=', 0)->pluck('title')->values(),
            ];
        })->values();

        // Web search fallback, unless the provider is the tile filter or search is off
        $fallback = null;
        if ((bool) Setting::fetch('homepage_search')) {
            $provider = Setting::fetch('search_provider');
            $details = $provider && $provider !== 'tiles' && $provider !== 'none'
                ? Search::providerDetails($provider)
                : false;
            if ($details) {
                $fallback = ['provider' => $provider, 'name' => $details->name, 'url' => url('search')];
            }
        }

        return response()->json([
            'items' => $items,
            'fallback' => $fallback,
            'target' => $this->linkTarget(),
        ]);
    }

    /**
     * The "Link opens in" setting as an anchor target
     */
    private function linkTarget(): string
    {
        $target = Setting::fetch('window_target');

        return in_array($target, [null, '', 'current'], true) ? '_self' : $target;
    }

    /**
     * Get autocomplete suggestions for a search query
     *
     * @return JsonResponse
     */
    public function autocomplete(Request $request)
    {
        $requestprovider = $request->input('provider');
        $query = $request->input('q');

        if (!$query || trim($query) === '') {
            return response()->json([]);
        }

        $provider = Search::providerDetails($requestprovider);

        if (!$provider || !isset($provider->autocomplete)) {
            return response()->json([]);
        }

        // Replace {query} placeholder with actual query
        $autocompleteUrl = str_replace('{query}', urlencode($query), $provider->autocomplete);

        try {
            $response = Http::timeout(5)->get($autocompleteUrl);

            if ($response->successful()) {
                $data = $response->body();
                
                // Parse the response based on provider
                $suggestions = $this->parseAutocompleteResponse($data, $provider->id);
                
                return response()->json($suggestions);
            }
        } catch (\Exception $e) {
            // Return empty array on error
            return response()->json([]);
        }

        return response()->json([]);
    }

    /**
     * Parse autocomplete response based on provider format
     *
     * @param string $data
     * @param string $providerId
     * @return array
     */
    private function parseAutocompleteResponse($data, $providerId)
    {
        $suggestions = [];

        switch ($providerId) {
            case 'google':
                // Google returns XML format
                if (strpos($data, '<?xml') === 0) {
                    $xml = simplexml_load_string($data);
                    if ($xml && isset($xml->CompleteSuggestion)) {
                        foreach ($xml->CompleteSuggestion as $suggestion) {
                            if (isset($suggestion->suggestion['data'])) {
                                $suggestions[] = (string) $suggestion->suggestion['data'];
                            }
                        }
                    }
                }
                break;

            case 'bing':
            case 'ddg':
                // Bing and DuckDuckGo return JSON array format
                $json = json_decode($data, true);
                if (is_array($json) && isset($json[1]) && is_array($json[1])) {
                    $suggestions = $json[1];
                }
                break;

            default:
                // Try to parse as JSON array
                $json = json_decode($data, true);
                if (is_array($json)) {
                    if (isset($json[1]) && is_array($json[1])) {
                        $suggestions = $json[1];
                    } else {
                        $suggestions = $json;
                    }
                }
                break;
        }

        return $suggestions;
    }
}
