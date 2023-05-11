<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'username', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * Get the items for the user.
     */
    public function items()
    {
        return $this->hasMany('App\Item');
    }

    /**
     * The settings that belong to the user.
     */
    public function settings()
    {
        return $this->belongsToMany('App\Setting')->withPivot('uservalue');
    }

    public static function currentUser()
    {
        $current_user = session('current_user');
        if ($current_user) { // if logged in, set this user
            return $current_user;
        } else { // not logged in, get first user
            $user = User::where('public_front',true)->first();
            if(!$user) {
                $user = User::first();
            }
            session(['current_user' => $user]);
            return $user;
        }

    }


}
