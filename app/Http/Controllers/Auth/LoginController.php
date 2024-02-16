<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\URL;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected string $redirectTo = '/';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        Session::put('backUrl', URL::previous());
        $this->middleware('guest')->except(['logout','autologin']);
    }

    public function username(): string
    {
        return 'username';
    }

    /**
     * Handle a login request to the application.
     *
     *
     * @throws ValidationException
     */
    public function login(Request $request): Response
    {
        $current_user = User::currentUser();
        $request->merge(['username' => $current_user->username, 'remember' => true]);
        //die(print_r($request->all()));
        $this->validateLogin($request);

        // If the class is using the ThrottlesLogins trait, we can automatically throttle
        // the login attempts for this application. We'll key this by the username and
        // the IP address of the client making these requests into this application.
        if ($this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);

            return $this->sendLockoutResponse($request);
        }

        if ($this->attemptLogin($request)) {
            return $this->sendLoginResponse($request);
        }

        // If the login attempt was unsuccessful we will increment the number of attempts
        // to login and redirect the user back to the login form. Of course, when this
        // user surpasses their maximum number of attempts they will get locked out.
        $this->incrementLoginAttempts($request);

        return $this->sendFailedLoginResponse($request);
    }

    public function index()
    {
    }

    public function setUser(User $user): RedirectResponse
    {
        Auth::logout();
        session(['current_user' => $user]);

        return redirect()->route('dash');
    }

    /**
     * @param $uuid
     */
    public function autologin($uuid): RedirectResponse
    {
        Auth::logout();

        $user = User::where('autologin', $uuid)->first();

        if (!$user) {
            return redirect()->route('dash');
        }

        Auth::login($user, true);

        session(['current_user' => $user]);

        return redirect()->route('dash');
    }

    /**
     * Show the application's login form.
     *
     * @return Application|Factory|View
     */
    public function showLoginForm(): \Illuminate\View\View
    {
        return view('auth.login');
    }

    /**
     * @param $user
     */
    protected function authenticated(Request $request, $user): RedirectResponse
    {
        return back();
    }

    /**
     * @return mixed|string
     */
    public function redirectTo()
    {
        return Session::get('url.intended') ? Session::get('url.intended') : $this->redirectTo;
    }
}
