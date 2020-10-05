<?php

namespace App\Http\Controllers;

use App\Http\Resources\User as UserResource;
use App\Role;
use App\Store;
use App\User;
use Auth;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function __construct()
    {

        $this->middleware('auth:api');

    }

    public function index()
    {

        $this->authorize('hasPermission', 'all');

        $user = User::findOrFail(Auth::user()->id);

        $store_id = $user->stores[0]->id;

        // $store = Store::findOrFail($store_id);

        // $users=$store->users();

        // return UserResource::collection($store->users()->with('roles')->paginate(8));

        // $users=$store->users()->with('roles')->paginate(1);

        // $users=collect($users)->filter(function($user,$key){

        //    return !collect($user['roles'])->contains('name','owner');

        // })->get();

        // $users=collect($users);

        // $users=$users->data->filter(function($user,$key){

        //    return !collect($user['roles'])->contains('name','owner');

        // });

        return UserResource::collection(Store::findOrFail($store_id)->users()->with('roles')->whereHas('roles',function($q){

            $q->where('roles.name','!=','owner');

        })->paginate(8));



    }

    public function store(Request $request)
    {
        $this->authorize('hasPermission', 'all'); //all permission belongs to owner only

        $user = User::findOrFail(Auth::user()->id);

        $store_id = $user->stores[0]->id;

        $store = Store::findOrFail($store_id);

        $this->validate($request, [

            'name'     => 'required|string|max:20',

            'email'    => 'required|email|max:100',

            'role_id'  => 'required|integer',

            'password' => 'required',
            // 'password'=>'required| min:6|regex:/^.*(?=.{3,})(?=.*[a-zA-Z])(?=.*[0-9])(?=.*[\d\X])(?=.*[!$#%]).*$/',
            //English uppercase characters (A – Z)

            // English lowercase characters (a – z)

            // Base 10 digits (0 – 9)

            // Non-alphanumeric (For example: !, $, #, or %)

            // Unicode characters
        ]);

        $user           = new User();
        $user->name     = $request->input('name');
        $user->email    = $request->input('email');
        $user->password = bcrypt($request->input('password'));

        $role = Role::findOrFail($request->input('role_id'));

        if ($user->save()) {

            $user->roles()->attach($role);

            $user->stores()->attach($store);

            return response()->json([
                'msg'    => 'Data Saved',
                'status' => 'success',
            ]);

        } else {

            return response()->json([
                'msg'    => 'Error Saving Data',
                'status' => 'eroor',
            ]);

        }

    }

    public function update(Request $request)
    {
        $this->authorize('hasPermission', 'all'); //all permission belongs to owner only

        $user = User::findOrFail(Auth::user()->id);

        $store_id = $user->stores[0]->id;

        $store = Store::findOrFail($store_id);

        $this->validate($request, [

            'name'        => 'required|string|max:20',

            'email'       => 'required|email|max:100',

            'role_id_old' => 'required|integer',

            'role_id'     => 'required|integer',

            'password'    => 'required',

            // 'password'=>'required| min:6|regex:/^.*(?=.{3,})(?=.*[a-zA-Z])(?=.*[0-9])(?=.*[\d\X])(?=.*[!$#%]).*$/',
            //English uppercase characters (A – Z)

            // English lowercase characters (a – z)

            // Base 10 digits (0 – 9)

            // Non-alphanumeric (For example: !, $, #, or %)

            // Unicode characters
        ]);

        $user_id = $request->input('id');

        $user = User::findOrFail($user_id);

        $user->name = $request->input('name');

        $user->email = $request->input('email');

        $user->password = bcrypt($request->input('password'));

        if ($user->save()) {

            $role_old = Role::findOrFail($request->input('role_id_old'));

            $user->roles()->detach($role_old);

            $role = Role::findOrFail($request->input('role_id'));

            $user->roles()->attach($role);

            return response()->json([
                'msg'    => 'Data updated',
                'status' => 'success',
            ]);

        } else {

            return response()->json([
                'msg'    => 'Error updating Data',
                'status' => 'eroor',
            ]);

        }
    }

    public function show($id)
    {

        $this->authorize('hasPermission', 'all'); //all permission belongs to owner only

        $user = User::where('id', $id)->with('roles')->first();

        return response()->json([
            'user'   => $user,
            'status' => 'success',
        ]);
    }

    public function destroy($id)
    {

        $this->authorize('hasPermission', 'all'); //all permission belongs to owner only

        $user = User::findOrFail($id); //finding passed user refrence

        $role_name_of_passed_user = $user->roles[0]->name;

        $store_id_of_passed_user = $user->stores[0]->id;

        //checking logged in user belong to that passed user id store or not
        $this->authorize('hasStore', $store_id_of_passed_user);

        // $this->authorize('hasPermission','delete_user');

        if ($role_name_of_passed_user != 'owner') {

            if ($user->delete()) {
                return response()->json([
                    'msg'    => 'successfully Deleted',
                    'status' => 'success',
                ]);
            } else {
                return response()->json([
                    'msg'    => 'Error while deleting data',
                    'status' => 'error',
                ]);
            }
        } else {
            return response()->json([
                'msg'    => 'You can\'t delete owner',
                'status' => 'error',
            ]);
        }

    }

    public function searchUsers(Request $request)
    {

        $this->authorize('hasPermission', 'all'); //all permission belongs to owner only

        $user = User::findOrFail(Auth::user()->id);

        $store_id = $user->stores[0]->id;

        $searchKey = $request->input('searchQuery');
        if ($searchKey != '') {
            return UserResource::collection(User::where('name', 'like', '%' . $searchKey . '%')->paginate(8));
        } else {
            return response()->json([
                'msg'    => 'Error while retriving Users. No Data Supplied as key.',
                'status' => 'error',
            ]);
        }
    }

}
