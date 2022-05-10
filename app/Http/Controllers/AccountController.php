<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Account;
use App\User;
use Auth;
use App\Http\Resources\Account as AccountResource;
use App\Transaction;

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

            $Transaction = new Transaction();
            $Transaction->transaction_type = 'opening_balance';
            $Transaction->image = $request->input('image');
            $Transaction->date = time();
            $Transaction->notes = $request->input('notes'); //some notes
            $Transaction->amount = $request->input('opening_balance');
            $Transaction->transaction_name = $request->input('transaction_name'); //some purpose
            $Transaction->account_id = $account->id;
            $Transaction->store_id = $store_id;

            if ($Transaction->save()) {
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
    public function destroy($id)
    {

        $this->authorize('hasPermission', 'delete_account');

        $user = User::findOrFail(Auth::user()->id);

        $store_id = $user->stores[0]->id;

        $account = Account::where('store_id', $store_id)->where('id', $id)->first();
        if ($account->delete()) {

            //no need to delete account transaction since its cascade but we will delete checking null if already delete or not
            $accountTransaction = Transaction::where('account_id', $id)->where('transaction_type','opening_balance')->where('store_id',$store_id)->first();
            if ($accountTransaction != null) {
              $accountTransaction->delete();
            } 
            return response()->json([
                'msg' => 'successfully Deleted',
                'status' => 'success',
            ]);

        } else {
            return response()->json([
                'msg' => 'Error while deleting data',
                'status' => 'error',
            ]);
        }

    }
    public function update(Request $request)
    {

        $this->authorize('hasPermission', 'edit_account');

        $user = User::findOrFail(Auth::user()->id);

        $store_id = $user->stores[0]->id;

        $this->validate($request, [
            'name' => 'required|regex:/^[\pL\s\-]+$/u',
            'holder_name' => 'required|string|max:200',
            'bank_name' => 'required|string|max:200',
            'bank_acc_num' => 'required|unique:accounts,bank_acc_num,'.$request->get('id'),
            'account_info' => 'required|string|max:400',
            'opening_balance' => 'required|numeric',
        ]);

        $id = $request->input('id'); //get id from edit modal
       
        $account = Account::where('store_id', $store_id)->where('id', $id)->first();
        $account->name = $request->input('name');
        $account->holder_name = $request->input('holder_name');
        $account->bank_name = $request->input('bank_name');
        $account->bank_acc_num = $request->input('bank_acc_num');
        $account->account_info = $request->input('account_info');
        $account->opening_balance = $request->input('opening_balance');
        $account->balance = $request->input('opening_balance'); //since at starting open and actual balance is same
     
        $account->store_id = $store_id;

        if ($account->save()) {
            $accountTransaction = Transaction::where('account_id', $account->id)->where('transaction_type', 'opening_balance')->where('store_id',$store_id)->first();
            $accountTransaction->amount = $request->input('opening_balance');
            if ($accountTransaction->save()) {
                return response()->json([
                    'msg' => 'Account updated successfully',
                    'status' => 'success',
                ]);

            } else {
                return response()->json([
                    'msg' => 'Error while updating account transaction',
                    'status' => 'error',
                ]);
            }
        } else {
            return response()->json([
                'msg' => 'Error while updating account',
                'status' => 'error',
            ]);
        }

    }
    public function show($id)
    {

        $this->authorize('hasPermission', 'show_account');

        $user = User::findOrFail(Auth::user()->id);

        $store_id = $user->stores[0]->id;

        $account = Account::where('store_id', $store_id)->where('id', $id)->first();

        // $invoice_amount=Invoice::where('store_id',$store_id)->where('account_id',$id)->sum('grand_total');
        
        // $paid_amount=AccountPayment::where('store_id',$store_id)->where('account_id',$id)->sum('amount');
        
        // $balance_due= $invoice_amount - $paid_amount + ( $account->opening_balance) ;

        if ($account->save()) {
            return response()->json([
                'account' => $account,
                // 'invoice_amount'=>$invoice_amount,
                // 'paid_amount'=>$paid_amount,
                // 'balance_due'=>$balance_due,
                'status' => 'success',
            ]);
        } else {
            return response()->json([
                'msg' => 'Error while retriving Account',
                'status' => 'error',
            ]);
        }
    }

    public function searchAccounts(Request $request)
    {

        $this->authorize('hasPermission', 'search_account');

        $user = User::findOrFail(Auth::user()->id);

        $store_id = $user->stores[0]->id;

        $searchKey = $request->input('searchQuery');
        if ($searchKey != '') {
            return AccountResource::collection(Account::where('store_id', $store_id)->where('name', 'like', '%' . $searchKey . '%')->paginate(8));
        } else {
            return response()->json([
                'msg' => 'Error while retriving Account. No Data Supplied as key.',
                'status' => 'error',
            ]);
        }
    }

    public function getAccountTransactions($account_id){
        
        // $this->authorize('hasPermission','view_customer_transactions');

        $user = User::findOrFail(Auth::user()->id);

        $store_id = $user->stores[0]->id;

        $Transaction=Transaction::where('account_id',$account_id)->where('store_id',$store_id)->get();
       
        $transactions=array();
       
        $balance=0.00;
        for($i=0;$i<$Transaction->count();$i++){
            if($Transaction[$i]->transaction_type==='opening_balance'){
                $opening_balance = $Transaction[$i]->amount;
                $balance=$opening_balance; 

            }
            if($Transaction[$i]->transaction_type==='income'){
                $balance=$balance + $Transaction[$i]->amount;
            }
            if($Transaction[$i]->transaction_type==='sales_payment'){
                $balance=$balance + $Transaction[$i]->amount;
            }
            
            if($Transaction[$i]->transaction_type==='purchase_payment'){
                $balance=$balance - $Transaction[$i]->amount;
            }

            if($Transaction[$i]->transaction_type==='expense'){
                $balance = $balance - $Transaction[$i]->amount;
            }
            $balance= number_format((float)$balance, 2, '.', '');
            
            $Transaction[$i]->balance=$balance;
            
            $transactions[$i]=$Transaction[$i];
        }

        return response()->json(['transactions'=>$transactions]);
    }

}
