<?php

namespace App\Http\Controllers;

use App\Http\Resources\Supplier as SupplierResource;
use App\Store;
use App\Supplier;
use App\User;
use Auth;
use Illuminate\Http\Request;

class SupplierController extends Controller
{

    public function __construct()
    {

        $this->middleware('auth:api');
    }

    public function index()
    {

        $this->authorize('hasPermission', 'view_suppliers');

        $user = User::findOrFail(Auth::user()->id);

        $store_id = $user->stores[0]->id;

        return SupplierResource::collection(Supplier::where('store_id', $store_id)->paginate(8));

    }

    public function store(Request $request)
    {

        $this->authorize('hasPermission', 'add_supplier)');

        $user = User::findOrFail(Auth::user()->id);

        $store_id = $user->stores[0]->id;

        $this->validate($request, [

            'name'    => 'required|regex:/^[\pL\s\-]+$/u',

            'address' => 'required|string|max:200',

            'phone'   => 'required|unique:customers,phone|digits:10',

            'details' => 'required|string|max:400',
        ]);

        $supplier = new Supplier();

        $supplier->name = $request->input('name');

        $supplier->address = $request->input('address');

        $supplier->phone = $request->input('phone');

        $supplier->details = $request->input('details');

        $supplier->store_id = $store_id;

        if ($supplier->save()) {

            return response()->json([

                'msg'    => 'Supplier added successfully',

                'status' => 'success',
            ]);

        } else {
            return response()->json([

                'msg'    => 'Error while adding supplier',

                'status' => 'error',
            ]);
        }
    }

    public function update(Request $request)
    {

        $this->authorize('hasPermission', 'edit_supplier)');

        $user = User::findOrFail(Auth::user()->id);

        $store_id = $user->stores[0]->id;

        $this->validate($request, [

            'name'    => 'required|regex:/^[\pL\s\-]+$/u',

            'address' => 'required|string|max:200',

            'phone'   => 'required|digits:10',

            'details' => 'required|string|max:400',

        ]);

        $id = $request->input('id'); //get id from edit modal

        $supplier = Supplier::where('id', $id)->where('store_id', $store_id)->first();

        $supplier->name = $request->input('name');

        $supplier->address = $request->input('address');

        $supplier->phone = $request->input('phone');

        $supplier->store_id = $store_id;

        if ($supplier->save()) {

            return response()->json([

                'msg'    => 'Supplier update successfully',

                'status' => 'success',
            ]);
        } else {

            return response()->json([

                'msg'    => 'Error while updating supplier',
                'status' => 'error',
            ]);
        }

    }

    public function destroy($id)
    {

        $this->authorize('hasPermission', 'delete_supplier)');

        $user = User::findOrFail(Auth::user()->id);

        $store_id = $user->stores[0]->id;

        $supplier = Supplier::where('id', $id)->where('store_id', $store_id)->first();
        if ($supplier->delete()) {
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

    public function show($id)
    {

        $this->authorize('hasPermission', 'show_supplier');

        $user = User::findOrFail(Auth::user()->id);

        $store_id = $user->stores[0]->id;

        $supplier = Supplier::where('id', $id)->where('store_id', $store_id)->first();

        if ($supplier) {
            return response()->json([
                'supplier' => $supplier,
                'status'   => 'success',
            ]);
        } else {
            return response()->json([
                'msg'    => 'Error while retriving Supplier',
                'status' => 'error',
            ]);
        }
    }

    public function searchSuppliers(Request $request)
    {

        $this->authorize('hasPermission', 'search_supplier)');

        $user = User::findOrFail(Auth::user()->id);

        $store_id = $user->stores[0]->id;

        $searchKey = $request->input('searchQuery');
        if ($searchKey != '') {
            return SupplierResource::collection(Supplier::where('store_id', $store_id)->where('name', 'like', '%' . $searchKey . '%')->paginate(8));
        } else {
            return response()->json([
                'msg'    => 'Error while retriving Suppliers. No Data Supplied as key.',
                'status' => 'error',
            ]);
        }
    }

}
