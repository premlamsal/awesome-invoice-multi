<?php

namespace App\Http\Controllers;

use App\CustomerPayment;
use App\CustomerTransaction;
use App\User;
use Auth;
use Illuminate\Http\Request;

class CustomerPaymentController extends Controller
{
    public function __construct()
    {

        $this->middleware('auth:api');

        // $this->authorize('hasPermission','delete_customer');

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

        // $this->authorize('hasPermission', 'add_customer_payment');

        $user = User::findOrFail(Auth::user()->id);

        $store_id = $user->stores[0]->id;

        $payment = new CustomerPayment();
        $payment->customer_id = $request->input('customer_id');
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
            $customerTransaction = new CustomerTransaction();
            $customerTransaction->transaction_type = 'payment';
            $customerTransaction->refID = $payment->id;
            $customerTransaction->amount = $payment->amount;
            $customerTransaction->customer_id = $payment->customer_id;
            $customerTransaction->store_id = $store_id;
            if ($customerTransaction->save()) {
                //success code
                return response()->json([
                    'msg' => 'Customer Payment & Transaction successfully added ',
                    'status' => 'success',
                ]);
            } else {
                //fail code
                return response()->json([
                    'msg' => 'Erros while saving customer transaction ',
                    'status' => 'error',
                ]);
            }

        } else {
            //fail code
            return response()->json([
                'msg' => 'Erros while saving customer payment ',
                'status' => 'error',
            ]);
        }

    }
    public function show(Request $request)
    {

        $user = User::findOrFail(Auth::user()->id);

        $store_id = $user->stores[0]->id;

        $payment = CustomerPayment::where('store_id', $store_id)->where('id', $request->payment_id)->get();

        return response()->json(['data' => $payment, 'status' => 'success']);

    }
    public function update($id)
    {

    }
}
