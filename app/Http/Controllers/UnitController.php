<?php

namespace App\Http\Controllers;

use App\Http\Resources\Unit as UnitResource;
use App\Unit;
use App\User;
use Auth;
use Illuminate\Http\Request;

class UnitController extends Controller
{
    public function __construct()
    {

        $this->middleware('auth:api');

    }

    public function index()
    {

        $this->authorize('hasPermission', 'view_units');

        $user = User::findOrFail(Auth::user()->id);

        $store_id = $user->stores[0]->id;

        return UnitResource::collection(Unit::where('store_id', $store_id)->paginate(8));
    }

    public function store(Request $request)
    {

        $this->authorize('hasPermission', 'add_unit');

        $user = User::findOrFail(Auth::user()->id);

        $store_id = $user->stores[0]->id;

        $this->validate($request, [

            'short_name' => 'required|string|max:10',

            'long_name'  => 'required|string|max:100',
        ]);

        $unit = new Unit();

        $unit->short_name = $request->input('short_name');

        $unit->long_name = $request->input('long_name');

        $unit->store_id = $store_id;

        if ($unit->save()) {

            return response()->json([
                'msg'    => 'You have successfully added the information.',
                'status' => 'success',
            ]);
        } else {
            return response()->json([
                'msg'    => 'Opps! My Back got cracked while working in Database',
                'status' => 'error',
            ]);
        }

    }

    public function show($id)
    {
        $this->authorize('hasPermission', 'show_unit');

        $user = User::findOrFail(Auth::user()->id);

        $store_id = $user->stores[0]->id;

        $unit = Unit::where('id', $id)->where('store_id', $store_id)->first();

        return response()->json([
            'unit'   => $unit,
            'status' => 'success',
        ]);
    }

    public function update(Request $request)
    {

        $this->authorize('hasPermission', 'edit_unit');

        $user = User::findOrFail(Auth::user()->id);

        $store_id = $user->stores[0]->id;

        $this->validate($request, [

            'short_name' => 'required|string|max:10',

            'long_name'  => 'required|string|max:100',
        ]);

        $id = $request->input('id');

        $unit = Unit::where('id', $id)->where('store_id', $store_id)->first();

        $unit->short_name = $request->input('short_name');

        $unit->long_name = $request->input('long_name');

        if ($unit->save()) {

            return response()->json([

                'msg'    => "Record Updated successfully",

                'status' => 'success',
            ]);
        } else {

            return response()->json([

                'msg'    => 'Error Updating Data',

                'status' => 'error',
            ]);
        }
    }

    public function destroy($id)
    {

        $this->authorize('hasPermission', 'delete_unit');

        $user = User::findOrFail(Auth::user()->id);

        $store_id = $user->stores[0]->id;

        $unit = Unit::where('id', $id)->where('store_id', $store_id)->first();

        if ($unit->delete()) {

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

    public function searchUnits(Request $request)
    {

        $this->authorize('hasPermission', 'search_unit');

        $user = User::findOrFail(Auth::user()->id);

        $store_id = $user->stores[0]->id;

        $searchKey = $request->input('searchQuery');
        if ($searchKey != '') {
            return UnitResource::collection(Unit::where('store_id', $store_id)->where('short_name', 'like', '%' . $searchKey . '%')->where('store_id', $store_id)->paginate(8));
        } else {
            return response()->json([
                'msg'    => 'Error while retriving Units. No Data Supplied as key.',
                'status' => 'error',
            ]);
        }
    }
}
