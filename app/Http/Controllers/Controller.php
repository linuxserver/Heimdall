<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    protected $user;

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $this->user = $this->user();
            //print_r($this->user);
            return $next($request);
        });
    }

    public function user()
    {
        return User::currentUser();
    }
}
