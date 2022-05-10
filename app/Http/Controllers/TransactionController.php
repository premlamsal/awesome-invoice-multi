<?php

namespace App\Http\Controllers;

use App\Account;
use App\Transaction;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\Transaction as TransactionResource;

class TransactionController extends Controller
{
    public function __construct()
    {

        $this->middleware('auth:api');

        // $this->authorize('hasPermission','delete_customer');

    }
    public function index()
    {

        $this->authorize('hasPermission', 'view_transactions');

        $user = User::findOrFail(Auth::user()->id);

        $store_id = $user->stores[0]->id;

        return TransactionResource::collection(Transaction::where('store_id', $store_id)
        ->where('transaction_type','!=','opening_balance')
        ->paginate(8));
    }

    public function store(Request $request)
    {

        $this->validate($request, [
            'notes' => 'required|string|max:400',
            'amount' => 'required|numeric',
            'date' => 'required',
            'account_id' => 'required|numeric',
            'transaction_type' => 'required|string:max:50',
            'transaction_name' => 'required|string:max:50'
        ]);

        // $this->authorize('hasPermission', 'add_customer_payment');

        $user = User::findOrFail(Auth::user()->id);

        $store_id = $user->stores[0]->id;
        //success code
        $transaction = new Transaction();
        if ($request->hasFile('image')) {
            $imageName = time() . '.' . $request->image->getClientOriginalExtension();
            $request->image->move(public_path('img'), $imageName);
            $transaction->image = $imageName;
        }
        $transaction->transaction_type = $request->input('transaction_type');
        $transaction->refID = 0;
        $transaction->amount = $request->amount;
        $transaction->transaction_name = $request->input('transaction_name');
        $transaction->account_id = $request->input('account_id');
        $transaction->notes = $request->input('notes');
        $transaction->store_id = $store_id;
        $transaction->date = $request->date;

        if ($transaction->save()) {

            $account = Account::where('id', $request->account_id)->first();
            if ($request->input('transaction_type') == 'income') {
                $account->balance = $account->balance + $request->input('amount');
            } elseif ($request->input('transaction_type') == 'expense') {
                $account->balance = $account->balance - $request->input('amount');
            }
            if ($account->save()) {
                //success code
                return response()->json([
                    'msg' => 'Transaction successfully added ',
                    'status' => 'success',
                ]);
            }

        } else {
            //fail code
            return response()->json([
                'msg' => 'Erros while saving transaction ',
                'status' => 'error',
            ]);
        }
    }
    public function update(Request $request)
    {

        $this->validate($request, [
            'notes' => 'required|string|max:400',
            'amount' => 'required|numeric',
            'date' => 'required',
            'account_id' => 'required|numeric',
            'transaction_type' => 'required|string:max:50',
            'transaction_name' => 'required|string:max:50'
        ]);
        // $this->authorize('hasPermission', 'add_customer_payment');
        $user = User::findOrFail(Auth::user()->id);
        $store_id = $user->stores[0]->id;
        //success code
        $transaction =  Transaction::where('id', $request->input('transaction_id'))->where('store_id', $store_id)->first();
        if ($request->hasFile('image')) {
            $imageName = time() . '.' . $request->image->getClientOriginalExtension();
            $request->image->move(public_path('img'), $imageName);
            $transaction->image = $imageName;
        }
        $old_transaction_account_id = $transaction->value('account_id');
        $old_transaction_amount = $transaction->value('amount');
        $old_transaction_type = $transaction->value('transaction_type');

        $transaction->transaction_type = $request->input('transaction_type');
        $transaction->refID = 0;
        $transaction->amount = $request->amount;
        $transaction->transaction_name = $request->input('transaction_name');
        $transaction->account_id = $request->input('account_id');
        $transaction->notes = $request->input('notes');
        $transaction->store_id = $store_id;
        $transaction->date = $request->date;

        if ($transaction->save()) {
            if ($old_transaction_account_id != $request->input('account_id')) {



                $account = Account::where('id', $old_transaction_account_id)->first();

                if ($old_transaction_type == 'income') {
                    $account->balance = $account->balance - $old_transaction_amount;
                } elseif ($old_transaction_type == 'expense') {
                    $account->balance = $account->balance + $old_transaction_amount;
                }

                $account->save();
            } elseif ($old_transaction_account_id == $request->input('account_id')) {
                $account = Account::where('id', $request->account_id)->first();
                if ($request->input('transaction_type') == 'income') {
                    $account->balance = $account->balance - $request->input('old_amount');
                    $account->balance = $account->balance + $request->input('amount');
                } elseif ($request->input('transaction_type') == 'expense') {
                    $account->balance = $account->balance + $request->input('old_amount');
                    $account->balance = $account->balance - $request->input('amount');
                }
                if ($account->save()) {
                    //success code
                    return response()->json([
                        'msg' => 'Transaction successfully updated ',
                        'status' => 'success',
                    ]);
                }
            }
        } else {
            //fail code
            return response()->json([
                'msg' => 'Erros while saving transaction ',
                'status' => 'error',
            ]);
        }
    }
    public function show($transaction_id)
    {

        $user = User::findOrFail(Auth::user()->id);

        $store_id = $user->stores[0]->id;

        $transaction = Transaction::where('store_id', $store_id)->where('id', $transaction_id)->get();

        return response()->json(['data' => $transaction, 'status' => 'success']);
    }
    public function destroy($transaction_id)
    {
        $this->authorize('hasPermission', 'delete_transaction');

        $user = User::findOrFail(Auth::user()->id);

        $store_id = $user->stores[0]->id;

        $transaction = Transaction::where('store_id', $store_id)->where('id', $transaction_id)->first();
        if ($transaction->delete()) {
            $account = Account::where('id', $transaction->account_id)->first();
            if ($transaction->transaction_type == 'income') {
                $account->balance = $account->balance - $transaction->amount;
            } elseif ($transaction->transaction_type == 'expense') {
                $account->balance = $account->balance + $transaction->amount;
            }
            if ($account->save()) {
                return response()->json([
                    'msg' => 'successfully Deleted',
                    'status' => 'success',
                ]);
            } else {
                return response()->json([
                    'msg' => 'Error while deleting customer transaction',
                    'status' => 'error',
                ]);
            }
        } else {
            return response()->json([
                'msg' => 'Error while deleting data',
                'status' => 'error',
            ]);
        }
    }
}
