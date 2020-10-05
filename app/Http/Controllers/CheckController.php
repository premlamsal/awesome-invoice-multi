<?php

namespace App\Http\Controllers;

use App\User;
use Auth;

class CheckController extends Controller
{

    public function __construct()
    {

        $this->middleware('auth:api');

    }

    public function stores()
    {

        $user = User::findOrFail(Auth::user()->id);

        $stores = $user->stores;

        return response()->json(['stores' => $stores, 'status' => 'success']);

    }
    public function checkUserForStore()
    {

        $user_id = Auth::user()->id; //get logged user id

        $store = UserStore::where('user_id', $user_id)->get(); //get store information for the user

        if (count($store) > 0) {

            print_r($store);

        } else {

            print_r("You don't have any store. Insted Create One");
        }

    }

    public function checkPermissions()
    {

        $permissions = Auth::user()->roles()->first();

        $permissions = $permissions->permissions()->first()->name;

        $permissions = explode(',', $permissions); //seperate name string by ',' and push them to array

        // print_r($permissions);

        // $permissions=['view_customer','edit_customer','delete_customer','add_customer','is_user'];

        return response()->json([

            'permissions' => $permissions,

            'status'      => 'success',
        ]);

    }

}
