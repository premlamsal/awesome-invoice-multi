<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Store extends Model
{
    public function users()
    {

        return $this->belongsToMany('App\User', 'user_store', 'store_id', 'user_id');
    }
}
