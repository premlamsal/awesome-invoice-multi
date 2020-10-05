<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{

    public function user()
    {

        return $this->belongsTo('\App\User');
    }

    public function permissions()
    {

        return $this->belongsToMany('App\Permission', 'role_permission', 'role_id', 'permission_id');
    }

    public function hasPermission($permission)
    {

        $permissions = $this->permissions()->first()->value('name');

        $permissions = explode(',', $permissions);

        if (in_array($permission, $permissions)) {

            return true;
        }
        return false;
    }
}
