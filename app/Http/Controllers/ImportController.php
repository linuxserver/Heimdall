<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class ImportController extends Controller
{
    /**
     * Instantiate a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->middleware('allowed');
    }

    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request): View
    {
        return view('items.import');
    }
}
