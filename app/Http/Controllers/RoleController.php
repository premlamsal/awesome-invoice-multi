<?php

namespace App\Http\Controllers;

use App\Http\Resources\Role as RoleResource;
use App\Permission;
use App\Role;
use App\User;
use Auth;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    public function __construct()
    {

        $this->middleware('auth:api');

    }

    public function index()
    {

        $this->authorize('hasPermission', 'view_roles');

        $user = User::findOrFail(Auth::user()->id);

        $store_id = $user->stores[0]->id;
        //this will send roles without role type: owner
        return RoleResource::collection(Role::where('store_id', $store_id)->where('name', '!=', 'owner')->with('permissions')->paginate(8));

        //this will send all roles including role type:owner
        // return RoleResource::collection(Role::where('store_id', $store_id)->with('permissions')->paginate(8));

    }

    public function store(Request $request)
    {

        $this->authorize('hasPermission', 'add_role');

        $user = User::findOrFail(Auth::user()->id);

        $store_id = $user->stores[0]->id;

        $this->validate($request, [

            'name' => 'required|string|max:10',

        ]);

        $role = new Role();

        $role->name = $request->input('name');

        $role->store_id = $store_id;

        $permission = Permission::findOrFail($request->input('permission_id'));

        if ($role->save()) {

            $role->permissions()->attach($permission);

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

    public function show($id)
    {

        $this->authorize('hasPermission', 'show_role');

        $user = User::findOrFail(Auth::user()->id);

        $store_id = $user->stores[0]->id;

        $role = Role::where('id', $id)->where('store_id', $store_id)->with('permissions')->first();

        return response()->json([
            'data'   => $role,
            'status' => 'success',
        ]);
    }

    public function update(Request $request)
    {

        $this->authorize('hasPermission', 'edit_role');

        $user = User::findOrFail(Auth::user()->id);

        $store_id = $user->stores[0]->id;

        $this->validate($request, [

            'name' => 'required|string|max:10',

        ]);

        $role = Role::findOrFail($request->input('id'));

        $role->name = $request->input('name');

        $role->store_id = $store_id;

        if ($role->save()) {

            $permission_old = Permission::findOrFail($request->input('permission_id_old'));

            // Detach a single role from the user...
            $role->permissions()->detach($permission_old);

            $permission = Permission::findOrFail($request->input('permission_id'));
            //attach new role....
            $role->permissions()->attach($permission);

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

    public function destroy($id)
    {

        $this->authorize('hasPermission', 'delete_role');

        $user = User::findOrFail(Auth::user()->id);

        $store_id = $user->stores[0]->id;

        $role = Role::where('id', $id)->where('store_id', $store_id)->first();

        if ($role->delete()) {

            $permission_id = $role->permissions()->value('permission_id');

            $permissions = Permission::findOrFail($permission_id);

            // print_r($permission_id);
            // Detach a single role from the user...
            $role->permissions()->detach($permissions);

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
    }

    public function searchPermissions(Request $request)
    {

        $this->authorize('hasPermission', 'search_role');

        $user = User::findOrFail(Auth::user()->id);

        $store_id = $user->stores[0]->id;

        $searchKey = $request->input('searchQuery');
        if ($searchKey != '') {
            return PermissionResource::collection(Permission::where('store_id', $store_id)->where('short_name', 'like', '%' . $searchKey . '%')->where('store_id', $store_id)->paginate(8));
        } else {
            return response()->json([
                'msg'    => 'Error while retriving Permissions. No Data Supplied as key.',
                'status' => 'error',
            ]);
        }
    }
}
