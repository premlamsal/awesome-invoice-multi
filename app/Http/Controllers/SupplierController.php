<?php

namespace App\Http\Controllers;

use App\Http\Resources\Supplier as SupplierResource;
use App\Invoice;
use App\Purchase;
use App\Store;
use App\Supplier;
use App\SupplierPayment;
use App\SupplierTransaction;
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

            'opening_balance' => 'required|numeric',

            'phone'   => 'required|unique:customers,phone|digits:10',

            'details' => 'required|string|max:400',

            'opening_balance' => 'required|numeric',

        ]);

        $supplier = new Supplier();

        $supplier->name = $request->input('name');

        $supplier->address = $request->input('address');

        $supplier->phone = $request->input('phone');

        $supplier->details = $request->input('details');

        $supplier->opening_balance = $request->input('opening_balance');

        $supplier->store_id = $store_id;

        if ($supplier->save()) {

            $SupplierTransaction = new SupplierTransaction();
            $SupplierTransaction->transaction_type = 'opening_balance';
            $SupplierTransaction->refID = '0';
            $SupplierTransaction->amount = $request->input('opening_balance');
            $SupplierTransaction->supplier_id = $supplier->id;
            $SupplierTransaction->store_id = $store_id;
            if ($SupplierTransaction->save()) {
                return response()->json([
                    'msg' => 'Supplier added successfully',
                    'status' => 'success',
                ]);

            }else{
                return response()->json([
                    'msg' => 'Error while adding Supplier transaction',
                    'status' => 'error',
                ]); 
            }

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

            'opening_balance' => 'required|numeric',


        ]);

        $id = $request->input('id'); //get id from edit modal

        $supplier = Supplier::where('id', $id)->where('store_id', $store_id)->first();

        $supplier->name = $request->input('name');

        $supplier->address = $request->input('address');

        $supplier->phone = $request->input('phone');

        $supplier->opening_balance = $request->input('opening_balance');

        $supplier->store_id = $store_id;

        if ($supplier->save()) {

            $SupplierTransaction = SupplierTransaction::where('supplier_id',$supplier->id)->where('transaction_type','opening_balance')->first();
            $SupplierTransaction->amount = $request->input('opening_balance');
            if ($SupplierTransaction->save()) {
                return response()->json([
                    'msg' => 'Supplier updated successfully',
                    'status' => 'success',
                ]);

            }else{
                return response()->json([
                    'msg' => 'Error while updating Supplier transaction',
                    'status' => 'error',
                ]); 
            }
        } else {

            return response()->json([

                'msg'    => 'Error while updating supplier',
                'status' => 'error',
            ]);
        }

    }
    public function getPayments($supplier_id){
        
        $user = User::findOrFail(Auth::user()->id);

        $store_id = $user->stores[0]->id;

        $SupplierPayments=SupplierPayment::where('store_id',$store_id)->where('supplier_id',$supplier_id)->get();
       
        return response()->json(['data'=>$SupplierPayments,'status'=>'success']);
    }


    public function destroy($id)
    {

        $this->authorize('hasPermission', 'delete_supplier)');

        $user = User::findOrFail(Auth::user()->id);

        $store_id = $user->stores[0]->id;

        $supplier = Supplier::where('id', $id)->where('store_id', $store_id)->first();
        if ($supplier->delete()) {
            $SupplierTransaction = SupplierTransaction::where('customer_id', $supplier->id)->where('transaction_type', 'opening_balance')->first();
            if ($SupplierTransaction->delete()) {
                return response()->json([
                    'msg' => 'successfully Deleted',
                    'status' => 'success',
                ]);
            } else {
                return response()->json([
                    'msg' => 'Error while deleting Supplier transaction',
                    'status' => 'error',
                ]);
            }
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

        $purchase_amount=Purchase::where('store_id',$store_id)->where('supplier_id',$id)->sum('grand_total');
        $paid_amount=SupplierPayment::where('store_id',$store_id)->where('supplier_id',$id)->sum('amount');
        $balance_due=floatval($purchase_amount)-floatval($paid_amount)-floatval($supplier->opening_balance);

        if ($supplier->save()) {
            return response()->json([
                'supplier' => $supplier,
                'purchase_amount'=>$purchase_amount,
                'paid_amount'=>$paid_amount,
                'balance_due'=>$balance_due,
                'status' => 'success',
            ]);
        } else {
            return response()->json([
                'msg' => 'Error while retriving Customer',
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
