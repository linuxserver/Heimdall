<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Search;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        $requestprovider = $request->input('provider');
        $query = $request->input('q');

        $provider = Search::providerDetails($requestprovider);

        if($provider->type == 'standard') {
            return redirect($provider->url.'?'.$provider->var.'='.urlencode($query));
        } elseif($provider->type == 'external') {
            $class = new $provider->class;
            //print_r($provider);
            return $class->getResults($query, $provider);
        }

        //print_r($provider);
    }
}
