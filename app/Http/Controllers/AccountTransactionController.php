<?php

namespace App\Http\Controllers;

use App\AccountTransaction;
use Illuminate\Http\Request;
use Auth;
use App\User;


class AccountTransactionController extends Controller
{
    public function __construct()
    {

        $this->middleware('auth:api');

    }

    public function index($account_id){
        
        // $this->authorize('hasPermission','view_customer_transactions');

        $user = User::findOrFail(Auth::user()->id);

        $store_id = $user->stores[0]->id;

        $AccountTransaction=AccountTransaction::where('account_id',$account_id)->where('store_id',$store_id)->get();
       
        $transactions=array();
       
        $balance=0.00;
        for($i=0;$i<$AccountTransaction->count();$i++){
            if($AccountTransaction[$i]->transaction_type==='opening_balance'){
                $opening_balance = $AccountTransaction[$i]->amount;
                $balance=$opening_balance; 

            }
            if($AccountTransaction[$i]->transaction_type==='income'){
                $balance=$balance + $AccountTransaction[$i]->amount;
            }
            if($AccountTransaction[$i]->transaction_type==='expense'){
                $balance = $balance - $AccountTransaction[$i]->amount;
            }
            $balance= number_format((float)$balance, 2, '.', '');
            
            $AccountTransaction[$i]->balance=$balance;
            
            $transactions[$i]=$AccountTransaction[$i];
        }

        return response()->json(['transactions'=>$transactions]);
    }
}
