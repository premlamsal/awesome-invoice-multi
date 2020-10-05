<?php

namespace App\Http\Controllers;

use App\Permission;
use App\Role;
use App\Store;
use App\User;
use Auth;
use Illuminate\Http\Request;

class PagesController extends Controller
{
    public function __construct()
    {

        $this->middleware('auth');

    }

    public function createStore()
    {

    }

    public function saveStore(Request $request)
    {

        $user_id = Auth::user()->id;

        $user = User::findOrFail($user_id);

        $store = new Store();

        $store->name = $request->input('name');

        $store->address = $request->input('address');

        $store->phone = $request->input('phone');

        $store->detail = $request->input('detail');

        $store->mobile = $request->input('mobile');

        $store->email = $request->input('email');

        $store->url = $request->input('url');

        $store->tax_number = $request->input('tax_number');

        $store->tax_percentage = $request->input('tax_percentage');

        $store->profit_percentage = $request->input('profit_percentage');

        $store->store_logo = null;

        if ($store->save()) {

            $user->stores()->attach($store);

            $role = new Role();

            $role->name = 'owner'; //owner has all privilledge to do.

            $role->store_id = $store->id; //storing store if from recently saved store.

            if ($role->save()) {

                $user->roles()->attach($role);

                $permission           = new Permission();
                $permission->name     = 'all';
                $permission->store_id = $store->id;
                if ($permission->save()) {

                    $role->permissions()->attach($permission);

                    return redirect('/');
                }
                abort(404);
            }
            abort(404);
        } else {
            // print_r("Store Creation Failed.");
            return back()->withInput();
        }
    }
}
