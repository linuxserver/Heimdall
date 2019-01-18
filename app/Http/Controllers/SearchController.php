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

        if($provider->type == 'external') {
            return redirect($provider->url.'?'.$provider->var.'='.urlencode($query));
        } else {
            // get results
        }

        //print_r($provider);
    }
}
