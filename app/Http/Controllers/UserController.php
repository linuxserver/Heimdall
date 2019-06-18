<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('allowed')->except(['selectUser']);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data['users'] = User::all();
        return view('users.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $data = [];
        return view('users.create', $data);
    }

    public function selectUser()
    {
        Auth::logout();
        $data['users'] = User::all();
        return view('userselect', $data);

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'username' => 'required|max:255|unique:users',
            'email' => 'required|email',
            'password' => 'nullable|confirmed',
            'password_confirmation' => 'nullable'

        ]);
        $user = new User;
        $user->username = $request->input('username');
        $user->email = $request->input('email');
        $user->public_front = $request->input('public_front');

        $password = $request->input('password');
        if(!empty($password)) {
            $user->password = bcrypt($password);
        }

        if($request->hasFile('file')) {
            $path = $request->file('file')->store('avatars');
            $user->avatar = $path;
        }

        if ((bool)$request->input('autologin_allow') === true) {
            $user->autologin = (string)Str::uuid();
        }

        $user->save();
        
        $route = route('dash', []);
        return redirect($route)
            ->with('success',__('app.alert.success.user_updated'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(User $user)
    {
        $data['user'] = $user;
        return view('users.edit', $data);

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
    {
        $validatedData = $request->validate([
            'username' => 'required|max:255|unique:users,username,'.$user->id,
            'email' => 'required|email',
            'password' => 'nullable|confirmed',
            'password_confirmation' => 'nullable'
        ]);
            //die(print_r($request->all()));

        $user->username = $request->input('username');
        $user->email = $request->input('email');
        $user->public_front = $request->input('public_front');

        $password = $request->input('password');
        if(!empty($password)) {
            $user->password = bcrypt($password);
        } elseif($password == 'null') {
            $user->password = null;
        }
    
        if($request->hasFile('file')) {
            $path = $request->file('file')->store('avatars');
            $user->avatar = $path;
        }

        if ((bool)$request->input('autologin_allow') === true) {
            $user->autologin = (is_null($user->autologin)) ? (string)Str::uuid() : $user->autologin;
        } else {
            $user->autologin = null;
        }

        $user->save();

        $route = route('dash', []);
        return redirect($route)
            ->with('success',__('app.alert.success.user_updated'));

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        if($user->id !== 1) {
            $user->delete();
            $route = route('dash', []);
            return redirect($route)
            ->with('success',__('app.alert.success.user_deleted'));

        }
    }
}
