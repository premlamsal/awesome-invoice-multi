<?php

namespace App\Http\Controllers;

use App\Account;
use App\SupplierPayment;
use App\SupplierTransaction;
use App\Transaction;
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
    public function store(Request $request)
    {
        $this->validate($request, [
            'notes' => 'required|string|max:400',
            'amount' => 'required|numeric',
            'date' => 'required',



        ]);

        // $this->authorize('hasPermission', 'add_supplier_payment');

        $user = User::findOrFail(Auth::user()->id);

        $store_id = $user->stores[0]->id;

        $payment = new SupplierPayment();
        $payment->supplier_id = $request->input('supplier_id');
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
            $supplierTransaction = new SupplierTransaction();
            $supplierTransaction->transaction_type = 'payment';
            $supplierTransaction->refID = $payment->id;
            $supplierTransaction->amount = $payment->amount;
            $supplierTransaction->supplier_id = $payment->supplier_id;
            $supplierTransaction->store_id = $store_id;
            $supplierTransaction->date = $payment->date;
            if ($supplierTransaction->save()) {

                $transaction = new Transaction();
                $transaction->image = $imageName;
                $transaction->transaction_type = 'purchase_payment';
                $transaction->refID = $payment->id;
                $transaction->amount = $request->amount;
                $transaction->transaction_name = 'Purchase Payment';
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
                            'msg' => 'Supplier Payment, Transaction & Account successfully added ',
                            'status' => 'success',
                        ]);
                    }
                }





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
            'date' => 'required',


        ]);

        // $this->authorize('hasPermission', 'add_supplier_payment');

        $user = User::findOrFail(Auth::user()->id);

        $store_id = $user->stores[0]->id;

        $payment = SupplierPayment::where('id', $request->input('payment_id'))->where('store_id', $store_id)->first();
        $payment->supplier_id = $request->input('supplier_id');
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
            $supplierTransaction = SupplierTransaction::where('supplier_id', $payment->supplier_id)
                ->where('transaction_type', 'payment')
                ->where('refID', $request->input('payment_id'))
                ->where('store_id', $store_id)
                ->first();
            $supplierTransaction->amount = $payment->amount;
            $supplierTransaction->date = $payment->date;
            if ($supplierTransaction->save()) {


                $transaction = Transaction::where('refID', $payment->id)->first();
                if ($request->hasFile('image')) {
                    $imageName = time() . '.' . $request->image->getClientOriginalExtension();
                    $request->image->move(public_path('img'), $imageName);
                    $transaction->image = $imageName;
                }
                $transaction->transaction_type = 'purchase_payment';
                $transaction->refID = $supplierTransaction->id;
                $transaction->amount = $request->amount;
                $transaction->transaction_name = 'Purchase Payment';
                $transaction->account_id = $request->input('account_id');
                $transaction->notes = $request->input('notes');
                $transaction->store_id = $store_id;
                $transaction->date = $request->date;

                if ($transaction->save()) {


                    //check if account has past payment if yes deduce the amount from that account

                    $account = Account::where('id', $request->old_account_id)->first();
                    $account->balance = $account->balance + $request->input('old_amount');
                    $account->save();


                    //inserting new amount to new account

                    $account = Account::where('id', $request->account_id)->first();
                    $account->balance = $account->balance - $request->input('amount');
                    $account->save();

                    return response()->json([
                        'msg' => 'Supplier Payment , Transaction & Accounts successfully updated ',
                        'status' => 'success',
                    ]);
                }



            




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
