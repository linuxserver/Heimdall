<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use App\User;
use Illuminate\Support\Facades\Route;
use Session;

class CheckAllowed
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $route = Route::currentRouteName();
        $current_user = User::currentUser();

        if(str_is('users*', $route)) {
            if($current_user->id !== 1) {
                return redirect()->route('dash');
            }
        }

        if($route == 'dash') {
            //print_r(User::all());
            //die("here".var_dump($current_user->password));
            if((bool)$current_user->public_front === true) return $next($request);
        }

        if(empty($current_user->password)) return $next($request);

        // Check if user is logged in as $current_user
        if (Auth::check()) {
            $loggedin_user = Auth::user();
            if($loggedin_user->id === $current_user->id) return $next($request);
        }

        return Auth::authenticate();

    }
}
