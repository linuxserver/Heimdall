<?php

namespace App\Http\Controllers;

use App\Item;
use App\User;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\RateLimiter;

class HealthController extends Controller
{
    private static function getUsers(): int
    {
        return User::count();
    }

    private static function getItems(): int
    {
        return Item::select('id')
            ->where('deleted_at', null)
            ->where('type', '0')
            ->count();
    }

    /**
     * Handle the incoming request.
     *
     * @return JsonResponse|Response
     * @throws BindingResolutionException
     */
    public function __invoke(Request $request)
    {
        $REQUESTS_MAX_PER_MIN = 30;
        $STATUS_TOO_MANY_REQUESTS = 429;

        if (RateLimiter::remaining('health', $REQUESTS_MAX_PER_MIN) < 1) {
            return response()->make('Too many attempts.', $STATUS_TOO_MANY_REQUESTS);
        }

        RateLimiter::hit('health');

        return response()->json([
            'status' => 'ok',
            'items' => self::getItems(),
            'users' => self::getUsers(),
        ]);
    }
}
