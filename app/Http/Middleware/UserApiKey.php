<?php

namespace App\Http\Middleware;

use Closure;
use \App\SettingUser;

class UserApiKey
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
        $key = $request->input('api_key');
        $details = SettingUser::where('setting_id', 12)->where('uservalue', $key)->first();
        // die(var_dump($details));
        if($details === null) {
            return response()->json([
                'status' => 401,
                'message' => 'invalid api key'
            ], 401);
        }
        return $next($request);
    }
}
