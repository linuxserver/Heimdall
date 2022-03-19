<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

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
        return $this->hasMany(\App\Item::class);
    }

    /**
     * The settings that belong to the user.
     */
    public function settings()
    {
        return $this->belongsToMany(\App\Setting::class)->withPivot('uservalue');
    }

    public static function currentUser()
    {
        $current_user = session('current_user');
        if ($current_user) { // if logged in, set this user
            return $current_user;
        } else { // not logged in, get first user
            $user = self::where('public_front', true)->first();
            if (! $user) {
                $user = self::first();
            }
            session(['current_user' => $user]);

            return $user;
        }
    }
}
