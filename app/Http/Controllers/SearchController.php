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

        $provider = Search::providerDetails($requestprovider);

        if ($provider->type == 'standard') {
            return redirect($provider->url.'?'.$provider->query.'='.urlencode($query));
        } elseif ($provider->type == 'external') {
            $class = new $provider->class;
            //print_r($provider);
            return $class->getResults($query, $provider);
        }

        //print_r($provider);
    }
}
