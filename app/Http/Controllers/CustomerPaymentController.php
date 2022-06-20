<?php

namespace App\Http\Controllers;

use App\Account;
use App\CustomerPayment;
use App\CustomerTransaction;
use App\Transaction;
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

    public function store(Request $request)
    {
        $this->validate($request, [
            'notes' => 'required|string|max:400',
            'amount' => 'required|numeric',
            'date' => 'required',
            'account_id' => 'required|numeric',

        ]);

        // $this->authorize('hasPermission', 'add_customer_payment');

        $user = User::findOrFail(Auth::user()->id);

        $store_id = $user->stores[0]->id;

        $payment = new CustomerPayment();
        $payment->customer_id = $request->input('customer_id');
        $payment->store_id = $store_id;
        $payment->amount = $request->input('amount');
        $payment->notes = $request->input('notes');
        $payment->date = $request->input('date');
        $imageName = "";
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
            $customerTransaction->date = $payment->date;
            if ($customerTransaction->save()) {
                $transaction = new Transaction();
                $transaction->image = $imageName;
                $transaction->transaction_type = 'sales_payment';
                $transaction->refID = $payment->id;
                $transaction->amount = $request->amount;
                $transaction->transaction_name = 'Sales Payment';
                $transaction->account_id = $request->input('account_id');
                $transaction->notes = $request->input('notes');
                $transaction->store_id = $store_id;
                $transaction->date = $request->date;
                if ($transaction->save()) {
                    $account = Account::where('id', $request->account_id)->first();
                    $account->balance = $account->balance + $request->input('amount');
                    if ($account->save()) {
                        //success code
                        return response()->json([
                            'msg' => 'Customer Payment, Transaction & Account successfully added ',
                            'status' => 'success',
                        ]);
                    }
                }
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

        //lets find our account_id  
        //since we have column 'refID' & 'account_id' on 'transaction' table
        //so we match both by suppling 'customerPayment' id to refID and get account_id

        $transaction = Transaction::where('refID', $request->payment_id)->where('store_id', $store_id);


        return response()->json(['data' => $payment, 'account_id' => $transaction->value('account_id'), 'status' => 'success']);
    }
    public function update(Request $request)
    {
        $this->validate($request, [
            'notes' => 'required|string|max:400',
            'amount' => 'required|numeric',
            'date' => 'required',
            'account' => 'required|numeric',
            'old_account' => 'required|numeric',
            'old_amount' => 'required|numeric',
        ]);

        // $this->authorize('hasPermission', 'add_customer_payment');

        $user = User::findOrFail(Auth::user()->id);

        $store_id = $user->stores[0]->id;

        $payment = CustomerPayment::where('id', $request->input('payment_id'))->where('store_id', $store_id)->first();
        $payment->customer_id = $request->input('customer_id');
        $payment->store_id = $store_id;
        $payment->amount = $request->input('amount');
        $payment->notes = $request->input('notes');
        $payment->date = $request->input('date');
        if ($request->hasFile('image')) {
            $imageName = time() . '.' . $request->image->getClientOriginalExtension();
            $request->image->move(public_path('img'), $imageName);
            $payment->image = $imageName;
        }
        if ($payment->save()) {
            //success code
            $customerTransaction = CustomerTransaction::where('customer_id', $payment->customer_id)
                ->where('transaction_type', 'payment')
                ->where('refID', $request->input('payment_id'))
                ->where('store_id', $store_id)
                ->first();
            $customerTransaction->amount = $payment->amount;
            $customerTransaction->date = $payment->date;
            if ($customerTransaction->save()) {



                $transaction = Transaction::where('refID', $payment->id)->first();
                if ($request->hasFile('image')) {
                    $imageName = time() . '.' . $request->image->getClientOriginalExtension();
                    $request->image->move(public_path('img'), $imageName);
                    $transaction->image = $imageName;
                }
                $transaction->transaction_type = 'sales_payment';
                $transaction->refID = $customerTransaction->id;
                $transaction->amount = $request->amount;
                $transaction->transaction_name = 'Sales Payment';
                $transaction->account_id = $request->input('account_id');
                $transaction->notes = $request->input('notes');
                $transaction->store_id = $store_id;
                $transaction->date = $request->date;

                if ($transaction->save()) {


                    //check if account has past payment if yes deduce the amount from that account

                    $account = Account::where('id', $request->old_account_id)->first();
                    $account->balance = $account->balance - $request->input('old_amount');
                    $account->save();


                    //inserting new amount to new account

                    $account = Account::where('id', $request->account_id)->first();
                    $account->balance = $account->balance + $request->input('amount');
                    $account->save();

                    return response()->json([
                        'msg' => 'Customer Payment , Transaction & Accounts successfully updated ',
                        'status' => 'success',
                    ]);
                }
            } else {
                //fail code
                return response()->json([
                    'msg' => 'Erros while updating customer transaction ',
                    'status' => 'error',
                ]);
            }
        } else {
            //fail code
            return response()->json([
                'msg' => 'Erros while updating customer payment ',
                'status' => 'error',
            ]);
        }
    }
    public function destroy($payment_id)
    {
        $this->authorize('hasPermission', 'delete_customer_payment');

        $user = User::findOrFail(Auth::user()->id);

        $store_id = $user->stores[0]->id;

        $CustomerPayment = CustomerPayment::where('store_id', $store_id)->where('id', $payment_id)->first();
        if ($CustomerPayment->delete()) {
            $customerTransaction = CustomerTransaction::where('transaction_type', 'payment')->where('refID', $payment_id)->where('customer_id', $CustomerPayment->customer_id)->first();
            if ($customerTransaction->delete()) {


                $transaction = Transaction::where('refID', $payment_id)->first();

                $transaction_account_id = $transaction->account_id;

                $transaction_amount = $transaction->amount;

                if ($transaction->delete()) {


                    //check if account has past payment if yes deduce the amount from that account

                    $account = Account::where('id', $transaction_account_id)->first();
                    $account->balance = $account->balance - $transaction_amount;

                    if ($account->save()) {


                        return response()->json([
                            'msg' => 'Deleted successfully!! ',
                            'status' => 'success',
                        ]);
                    }
                }
            } else {
                return response()->json([
                    'msg' => 'Error while deleting customer transaction for payment',
                    'status' => 'error',
                ]);
            }
        } else {
            return response()->json([
                'msg' => 'Error while deleting customer payment',
                'status' => 'error',
            ]);
        }
    }
}
