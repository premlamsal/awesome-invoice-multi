<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Account;
use App\User;
use Auth;
use App\Transaction;
use App\Http\Resources\Account as AccountResource;

class AccountController extends Controller
{
    public function __construct()
    {

        $this->middleware('auth:api');

        // $this->authorize('hasPermission','delete_customer');

    }
    public function index(){
        
        $this->authorize('hasPermission', 'view_accounts');

        $user = User::findOrFail(Auth::user()->id);

        $store_id = $user->stores[0]->id;

        return AccountResource::collection(Account::where('store_id', $store_id)->paginate(8));

    }
    public function store(Request $request)
    {
        $this->authorize('hasPermission', 'add_account');

        $user = User::findOrFail(Auth::user()->id);

        $store_id = $user->stores[0]->id;

        $this->validate($request, [
            'name' => 'required|regex:/^[\pL\s\-]+$/u',
            'holder_name' => 'required|string|max:200',
            'bank_name' => 'required|string|max:200',
            'bank_acc_num' => 'required|unique:accounts',
            'account_info' => 'required|string|max:400',
            'opening_balance' => 'required|numeric',
            'balance' => 'required|numeric',
        ]);

        $account = new Account();
        $account->name = $request->input('name');
        $account->holder_name = $request->input('holder_name');
        $account->bank_name = $request->input('bank_name');
        $account->bank_acc_num = $request->input('bank_acc_num');
        $account->account_info = $request->input('account_info');
        $account->opening_balance = $request->input('opening_balance');
        $account->balance = $request->input('opening_balance'); //since at starting open and actual balance is same
        $account->store_id = $store_id;

        if ($account->save()) {

            $transaction = new Transaction();
            $transaction->transaction_type = 'opening_balance';
            $transaction->image = $request->input('image');
            $transaction->notes = $request->input('notes');
            $transaction->amount = $request->input('opening_balance');
            $transaction->purpose = $request->input('purpose');
            $transaction->account_id = $account->id;
            $transaction->store_id = $store_id;
            
            if ($transaction->save()) {
                return response()->json([
                    'msg' => 'Account added successfully',
                    'status' => 'success',
                ]);

            } else {
                return response()->json([
                    'msg' => 'Error while adding account transaction',
                    'status' => 'error',
                ]);
            }

        } else {
            return response()->json([
                'msg' => 'Error while adding account',
                'status' => 'error',
            ]);
        }

    }
}
