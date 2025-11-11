<?php

namespace App\Http\Controllers;

use App\Search;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Http;

class SearchController extends Controller
{
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
