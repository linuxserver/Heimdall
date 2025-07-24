<?php

namespace App\Http\Controllers;

use App\Search;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;

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

        // Validate the presence and non-emptiness of the query parameter
        if (!$query || trim($query) === '') {
            abort(400, 'Missing or empty query parameter');
        }

        $provider = Search::providerDetails($requestprovider);

        if (!$provider || !isset($provider->type)) {
            abort(404, 'Invalid provider');
        }

        if ($provider->type == 'standard') {
            return redirect($provider->url.'?'.$provider->query.'='.urlencode($query));
        } elseif ($provider->type == 'external') {
            $class = new $provider->class;
            return $class->getResults($query, $provider);
        }

        abort(404, 'Provider type not supported');}
}
