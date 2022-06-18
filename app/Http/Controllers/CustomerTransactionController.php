<?php

namespace App\Http\Controllers;

use App\CustomerTransaction;
use App\User;
use Illuminate\Http\Request;
use Auth;

class CustomerTransactionController extends Controller
{
    public function __construct()
    {

        $this->middleware('auth:api');

    }
    public function index($customer_id){
        
        // $this->authorize('hasPermission','view_customer_transactions');

        $user = User::findOrFail(Auth::user()->id);

        $store_id = $user->stores[0]->id;

        $CustomerTransaction=CustomerTransaction::where('customer_id',$customer_id)->where('store_id',$store_id)->get();
       
        $transactions=array();
       
        $balance=0.00;
        
        for($i=0;$i<$CustomerTransaction->count();$i++){
            if($CustomerTransaction[$i]->transaction_type==='opening_balance'){
                $opening_balance = $CustomerTransaction[$i]->amount;
                $balance=$opening_balance; 

            }
            if($CustomerTransaction[$i]->transaction_type==='sales'){
                $balance=$balance + $CustomerTransaction[$i]->amount;
            }
            if($CustomerTransaction[$i]->transaction_type==='sales_return'){
                $balance=$balance - $CustomerTransaction[$i]->amount;
            }
            if($CustomerTransaction[$i]->transaction_type==='payment'){
                $balance = $balance - $CustomerTransaction[$i]->amount;
            }
            $balance= number_format((float)$balance, 2, '.', '');
            
            $CustomerTransaction[$i]->balance=$balance;
            
            $transactions[$i]=$CustomerTransaction[$i];
        }

        return response()->json(['transactions'=>$transactions]);
    }
}
