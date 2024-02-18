<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class UserController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->middleware('allowed')->except(['selectUser']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $data['users'] = User::all();

        return view('users.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $data = [];

        return view('users.create', $data);
    }

    public function selectUser(): \Illuminate\View\View
    {
        Auth::logout();
        $data['users'] = User::all();

        return view('userselect', $data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validatedData = $request->validate([
            'username' => 'required|max:255|unique:users',
            'email' => 'required|email',
            'password' => 'nullable|confirmed',
            'password_confirmation' => 'nullable',
            'file' => 'image'
        ]);
        $user = new User;
        $user->username = $request->input('username');
        $user->email = $request->input('email');
        $user->public_front = $request->input('public_front');

        $password = $request->input('password');
        if (! empty($password)) {
            $user->password = bcrypt($password);
        }

        if ($request->hasFile('file')) {
            $path = $request->file('file')->store('avatars', 'public');
            $user->avatar = $path;
        }

        if ((bool) $request->input('autologin_allow') === true) {
            $user->autologin = (string) Str::uuid();
        }

        $user->save();

        $route = route('dash', []);

        return redirect($route)
            ->with('success', __('app.alert.success.user_updated'));
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id): void
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user): View
    {
        $data['user'] = $user;

        return view('users.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user): RedirectResponse
    {
        $validatedData = $request->validate([
            'username' => 'required|max:255|unique:users,username,'.$user->id,
            'email' => 'required|email',
            'password' => 'nullable|confirmed',
            'password_confirmation' => 'nullable',
            'file' => 'image'
        ]);
        //die(print_r($request->all()));

        $user->username = $request->input('username');
        $user->email = $request->input('email');
        $user->public_front = $request->input('public_front');

        $password = $request->input('password');
        if (! empty($password)) {
            $user->password = bcrypt($password);
        } elseif ($password == 'null') {
            $user->password = null;
        }

        if ($request->hasFile('file')) {
            $path = $request->file('file')->store('avatars', 'public');
            $user->avatar = $path;
        }

        if ((bool) $request->input('autologin_allow') === true) {
            $user->autologin = (is_null($user->autologin)) ? (string) Str::uuid() : $user->autologin;
        } else {
            $user->autologin = null;
        }

        $user->save();

        $route = route('dash', []);

        return redirect($route)
            ->with('success', __('app.alert.success.user_updated'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return RedirectResponse | void
     */
    public function destroy(User $user): RedirectResponse
    {
        if ($user->id !== 1) {
            $user->delete();
            $route = route('dash', []);

            return redirect($route)
            ->with('success', __('app.alert.success.user_deleted'));
        }
    }
}
