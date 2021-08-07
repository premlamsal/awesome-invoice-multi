<?php

namespace App\Http\Controllers;

use App\SupplierPayment;
use App\SupplierTransaction;
use App\User;
use Auth;
use Illuminate\Http\Request;

class SupplierPaymentController extends Controller
{
    public function __construct()
    {

        $this->middleware('auth:api');

        // $this->authorize('hasPermission','delete_supplier');

    }

    public function index()
    {

    }
    public function store(Request $request)
    {
        $this->validate($request, [
            'notes' => 'required|string|max:400',
            'amount' => 'required|numeric',
        ]);

        // $this->authorize('hasPermission', 'add_supplier_payment');

        $user = User::findOrFail(Auth::user()->id);

        $store_id = $user->stores[0]->id;

        $payment = new SupplierPayment();
        $payment->supplier_id = $request->input('supplier_id');
        $payment->store_id = $store_id;
        $payment->amount = $request->input('amount');
        $payment->notes = $request->input('notes');
        if ($request->hasFile('image')) {
            $imageName = time() . '.' . $request->image->getClientOriginalExtension();
            $request->image->move(public_path('img'), $imageName);
            $payment->image = $imageName;
        }
        if ($payment->save()) {
            //success code
            $supplierTransaction = new SupplierTransaction();
            $supplierTransaction->transaction_type = 'payment';
            $supplierTransaction->refID = $payment->id;
            $supplierTransaction->amount = $payment->amount;
            $supplierTransaction->supplier_id = $payment->supplier_id;
            $supplierTransaction->store_id = $store_id;
            if ($supplierTransaction->save()) {
                //success code
                return response()->json([
                    'msg' => 'Supplier Payment & Transaction successfully added ',
                    'status' => 'success',
                ]);
            } else {
                //fail code
                return response()->json([
                    'msg' => 'Erros while saving supplier transaction ',
                    'status' => 'error',
                ]);
            }

        } else {
            //fail code
            return response()->json([
                'msg' => 'Erros while saving supplier payment ',
                'status' => 'error',
            ]);
        }

    }
    public function show(Request $request)
    {

        $user = User::findOrFail(Auth::user()->id);

        $store_id = $user->stores[0]->id;

        $payment = SupplierPayment::where('store_id', $store_id)->where('id', $request->payment_id)->get();

        return response()->json(['data' => $payment, 'status' => 'success']);

    }
    public function update(Request $request)
    {
        $this->validate($request, [
            'notes' => 'required|string|max:400',
            'amount' => 'required|numeric',
        ]);

        // $this->authorize('hasPermission', 'add_supplier_payment');

        $user = User::findOrFail(Auth::user()->id);

        $store_id = $user->stores[0]->id;

        $payment = SupplierPayment::where('id', $request->input('payment_id'))->where('store_id', $store_id)->first();
        $payment->supplier_id = $request->input('supplier_id');
        $payment->store_id = $store_id;
        $payment->amount = $request->input('amount');
        $payment->notes = $request->input('notes');
        if ($request->hasFile('image')) {
            $imageName = time() . '.' . $request->image->getClientOriginalExtension();
            $request->image->move(public_path('img'), $imageName);
            $payment->image = $imageName;
        }
        if ($payment->save()) {
            //success code
            $supplierTransaction = SupplierTransaction::where('supplier_id', $payment->supplier_id)
                ->where('transaction_type', 'payment')
                ->where('refID', $request->input('payment_id'))
                ->where('store_id',$store_id)
                ->first();
            $supplierTransaction->amount = $payment->amount;
            if ($supplierTransaction->save()) {
                //success code
                return response()->json([
                    'msg' => 'Supplier Payment & Transaction successfully updated ',
                    'status' => 'success',
                ]);
            } else {
                //fail code
                return response()->json([
                    'msg' => 'Erros while updating supplier transaction ',
                    'status' => 'error',
                ]);
            }

        } else {
            //fail code
            return response()->json([
                'msg' => 'Erros while updating supplier payment ',
                'status' => 'error',
            ]);
        }

    }
    public function destroy($payment_id)
    {
        $this->authorize('hasPermission', 'delete_supplier_payment');

        $user = User::findOrFail(Auth::user()->id);

        $store_id = $user->stores[0]->id;

        $SupplierPayment = SupplierPayment::where('store_id', $store_id)->where('id', $payment_id)->first();
        if ($SupplierPayment->delete()) {
            $supplierTransaction = SupplierTransaction::where('transaction_type', 'payment')->where('refID', $payment_id)->where('supplier_id', $SupplierPayment->supplier_id)->first();
            if ($supplierTransaction->delete()) {
                return response()->json([
                    'msg' => 'successfully Deleted supplier payment',
                    'status' => 'success',
                ]);
            } else {
                return response()->json([
                    'msg' => 'Error while deleting supplier transaction for payment',
                    'status' => 'error',
                ]);
            }

        } else {
            return response()->json([
                'msg' => 'Error while deleting supplier payment',
                'status' => 'error',
            ]);
        }
    }
}
