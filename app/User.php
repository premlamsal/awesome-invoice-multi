<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'type',
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
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function stores()
    {

        return $this->belongsToMany('App\Store', 'user_store', 'user_id', 'store_id');
    }

    public function hasStore($store)
    {

        $check = $this->stores()->where('store_id', $store);

        if ($check->first()) {

            return true;
        }
        return false;
    }

    public function roles()
    {

        return $this->belongsToMany('App\Role', 'role_user', 'user_id', 'role_id');
    }

}
