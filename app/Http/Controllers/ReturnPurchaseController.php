<?php

namespace App\Http\Controllers;


use App\Http\Resources\ReturnPurchase as ReturnPurchaseResource;
use App\ReturnPurchase;
use App\ReturnPurchaseDetail;
use App\Stock;
use App\StockHistory;
use App\Store;
use App\Supplier;
use App\SupplierTransaction;
use App\User;
use Auth;
use Illuminate\Http\Request;

class ReturnPurchaseController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function index()
    {
        $this->authorize('hasPermission', 'view_return_purchases');

        $user = User::findOrFail(Auth::user()->id);

        $store_id = $user->stores[0]->id;

        return ReturnPurchaseResource::collection(ReturnPurchase::where('store_id', $store_id)->with('returnPurchaseDetail')->orderBy('updated_at', 'desc')->paginate(8));
    }

    public function store(Request $request)
    {
        $return_purchase_status_save = false;

        $this->authorize('hasPermission', 'add_return_purchase');

        $user = User::findOrFail(Auth::user()->id);

        $store_id = $user->stores[0]->id;
        //validation
        $this->validate($request, [

            'info.note' => 'required | string |max:200',
            'info.supplier_name' => 'required | string| max:200',
            'info.supplier_id' => 'required',
            'info.due_date' => 'required | date',
            'info.return_purchase_date' => 'required | date',

            'info.return_purchase_reference_number' => 'required | string| max:200',

            'info.discount' => 'required | numeric| max:200',

            'items.*.product_name' => 'required | string |max:200',
            'items.*.price' => 'required | numeric',
            'items.*.quantity' => 'required | numeric',

        ]);

        $store = Store::findOrFail($store_id);
        $store_tax_percentage = $store->tax_percentage;

        $store_tax = $store_tax_percentage / 100;

        //old invoice id
        $return_purchase_id_count = $store->return_purchase_id_count;

        //explode invoice id from database

        $custom_return_purchase_id = explode('-', $return_purchase_id_count);

        $custom_return_purchase_id[1] = $custom_return_purchase_id[1] + 1; //increase return_purchase

        //new custom_return_purchase_id
        $new_count_return_purchase_id = implode('-', $custom_return_purchase_id);

        //collecting data
        $items = collect($request->items)->transform(function ($item) {
            $item['line_total'] = $item['quantity'] * $item['price'];
            return new ReturnPurchaseDetail($item);
        });

        if ($items->isEmpty()) {
            return response()
                ->json([
                    'items_empty' => 'One or more Item is required.',
                ], 422);
        }

        $data = $request->info;

        $data['sub_total'] = $items->sum('line_total');

        $data['tax_amount'] = $data['sub_total'] * $store_tax;

        $data['grand_total'] = $data['sub_total'] + $data['tax_amount'] - $data['discount'];

        $data['store_id'] = $store_id;

        $data['return_purchase_reference_id'] = $data['supplier_short_name'] . '-' . $data['return_purchase_reference_number'];

        $data['custom_return_purchase_id'] = $new_count_return_purchase_id;

        $return_purchase = ReturnPurchase::create($data);

        $return_purchase->returnPurchaseDetail()->saveMany($items);

        //for inserting in stock and altering if already has one initialized stock and previous stock
        $items = collect($request->items);

        $countItems = count($items);

        $timeStamp = now();

        $jsonResponse = array();

        for ($i = 0; $i < $countItems; $i++) {

            $p_id = $items[$i]['product_id'];

            $stock = Stock::where('store_id', $store_id)->where('product_id', $p_id);

            //retirving current product-> stock quantity
            $in_stock_quantity = $stock->value('quantity');

            //get stock id
            $stock_id = $stock->value('id');

            $stock_price_old = $stock->value('price');

            //adding current stock with new return_purchased product quantity
            $new_stock_quantity = $in_stock_quantity - $items[$i]['quantity'];

            //found product on stock
            if ($stock_id != 0) {
                //found product that have same price on stock so updating the quanity for the product but same price
                if ($stock_price_old == $items[$i]['price']) {

                    $stock = Stock::findOrFail($stock_id);

                    $stock->quantity = $new_stock_quantity;

                    $stock->updated_at = $timeStamp;

                    if ($stock->save()) {
                        //set current return_purchase_id_count to store table
                        $store->return_purchase_id_count = $new_count_return_purchase_id;
                        if ($store->save()) {

                            $return_purchase_status_save = true;
                        } else {
                            $jsonResponse = ['msg' => 'Failed updating the Data to the store.', 'status' => 'error3'];
                        }
                    } else {

                        $jsonResponse = ['msg' => 'Failed Saving the Data to the Stock.', 'status' => 'error3'];
                    }
                } else { //the price is diff so we have to add new stock for the new price of that product

                    $stock = new Stock();

                    $stock->quantity = $new_stock_quantity;

                    $stock->updated_at = $timeStamp;

                    $stock->product_id = $p_id;

                    $stock->quantity = $new_stock_quantity;

                    $stock->price = $items[$i]['price'];

                    $stock->unit_id = $items[$i]['unit_id'];

                    $stock->created_at = $timeStamp;

                    $stock->updated_at = $timeStamp;

                    $stock->store_id = $store_id;

                    if ($stock->save()) {
                        //set current return_purchase_id_count to store table
                        $store->return_purchase_id_count = $new_count_return_purchase_id;
                        if ($store->save()) {

                            $return_purchase_status_save = true;
                        } else {
                            $jsonResponse = ['msg' => 'Failed updating the Data to the store.', 'status' => 'error3'];
                        }
                    } else {

                        $jsonResponse = ['msg' => 'Failed Saving the Data to the Stock.', 'status' => 'error3'];
                    }
                }
            } else {

                //couldn't find the product on stock

            }
        }
        if ($return_purchase_status_save) {
            $SupplierTransaction = new SupplierTransaction();
            $SupplierTransaction->transaction_type = "purchase_return";
            $SupplierTransaction->refId = $return_purchase->id;
            $SupplierTransaction->amount = $data['grand_total'];
            $SupplierTransaction->supplier_id = $data['supplier_id'];
            $SupplierTransaction->store_id = $data['store_id'];
            $SupplierTransaction->date = $data['return_purchase_date'];
            if ($SupplierTransaction->save()) {
                $jsonResponse = ['msg' => 'Successfully created return_purchase', 'status' => 'success'];
            } else {

                $jsonResponse = ['msg' => 'Error adding return_purchase to supplier transaction.', 'status' => 'error'];
            }
        }

        return response()->json($jsonResponse);
    }
    public function update(Request $request)
    {

        $this->authorize('hasPermission', 'edit_return_purchase');

        $user = User::findOrFail(Auth::user()->id);

        $store_id = $user->stores[0]->id;
        // //validation
        $this->validate($request, [

            'info.note' => 'required | string |max:200',
            'info.supplier_name' => 'required | string| max:200',
            'info.supplier_id' => 'required',
            'info.due_date' => 'required | date',
            'info.return_purchase_date' => 'required | date',

            'info.discount' => 'required | numeric| max:200',

            'items.*.product_name' => 'required | string |max:200',
            'items.*.price' => 'required | numeric',
            'items.*.quantity' => 'required | numeric',

        ]);
        $id = $request->id; //we will get return_purchase id here

        $return_purchase = ReturnPurchase::where('id', $id)->where('store_id', $store_id)->first();

        $items = collect($request->items)->transform(function ($item) {
            $item['line_total'] = $item['quantity'] * $item['price'];
            return new ReturnPurchaseDetail($item);
        });

        if ($items->isEmpty()) {
            return response()
                ->json([
                    'items_empty' => ['One or more Item is required.'],
                ], 422);
        }

        $store = Store::findOrFail($store_id);

        $store_tax_percentage = $store->tax_percentage;

        $store_tax = $store_tax_percentage / 100;

        $data = $request->info;

        $data['sub_total'] = $items->sum('line_total');
        $data['tax_amount'] = $data['sub_total'] * $store_tax;
        $data['grand_total'] = $data['sub_total'] + $data['tax_amount'] - $data['discount'];
        $data['store_id'] = $store_id;

        //first get old items
        // Get ReturnPurchase
        $ReturnPurchase = ReturnPurchase::where('id', $id)->where('store_id', $store_id)->first();

        //get return_purchase details
        $return_purchaseDetail = ReturnPurchaseDetail::where('return_purchase_id', $id)->get();

        $countItems = count($return_purchaseDetail);

        $check_save_stock = false;

        // $timeStamp=now();
        if ($countItems != 0) {

            for ($i = 0; $i < $countItems; $i++) {
                //get product id from each return_purchase details
                $p_id = $return_purchaseDetail[$i]['product_id'];

                $old_return_purchase_detail_qty = $return_purchaseDetail[$i]['quantity'];

                //finding stock to decrease the quantity of this return_purchase
                $stock = Stock::where('product_id', $p_id)->where('store_id', $store_id);

                $stock_id = $stock->value('id');

                $stock_qty = $stock->value('quantity');

                $old_stock_qty = $stock_qty - $old_return_purchase_detail_qty;

                $stock = Stock::where('id', $stock_id)->where('store_id', $store_id)->first();

                $stock->quantity = $old_stock_qty - $items[$i]['quantity'];

                if ($stock->save()) {
                    $check_save_stock = true;
                } else {
                    $check_save_stock = false;
                }
            }
            if ($check_save_stock) {

                $return_purchase->update($data);

                ReturnPurchaseDetail::where('return_purchase_id', $return_purchase->id)->delete();

                $return_purchase->returnPurchaseDetail()->saveMany($items);

                $SupplierTransaction = SupplierTransaction::where('refID', $return_purchase->id)->where('store_id', $store_id)->first();
                // $SupplierTransaction->transaction_type = "sales";
                // $SupplierTransaction->refId = $return_purchase->id;
                $SupplierTransaction->amount = $data['grand_total'];
                $SupplierTransaction->supplier_id = $data['supplier_id'];
                // $SupplierTransaction->store_id = $data['store_id'];
                $SupplierTransaction->date = $data['return_purchase_date'];
                if ($SupplierTransaction->save()) {
                    return response()->json(['msg' => 'You have successfully updated the ReturnPurchase.', 'status' => 'success']);
                } else {
                    return response()->json(['msg' => 'Error adding return_purchase to supplier transaction.', 'status' => 'success'], 500);
                }
            } else {
                //saving stock fails
                return response()->json(['msg' => 'Initial update to stock failed.', 'status' => 'error'], 500);
            }

            // check stock save status and do following

        } else {

            return response()->json([
                'msg' => 'Update Failed. There is no items in this return_purchase',
                'status' => 'error',
            ], 500);
        }
    }
  
    public function show($id)
    {

        $this->authorize('hasPermission', 'show_return_purchase');

        $user = User::findOrFail(Auth::user()->id);

        $store_id = $user->stores[0]->id;
        // Get ReturnPurchase

        $ReturnPurchase = ReturnPurchase::where('store_id', $store_id)->with('returnPurchaseDetail.product.unit')->with('supplier')->findOrFail($id);
        $supplier_id = $ReturnPurchase->supplier_id;
        $Supplier = Supplier::where('id', $supplier_id)->where('store_id', $store_id);

        return response()
            ->json([
                'return_purchase' => $ReturnPurchase,
                'supplier' => $Supplier,

            ]);
    }

    public function destroy($id)
    {

        $user = User::findOrFail(Auth::user()->id);

        $store_id = $user->stores[0]->id;
        // Get ReturnPurchase
        $ReturnPurchase = ReturnPurchase::where('id', $id)->where('store_id', $store_id)->first();

        //get return_purchase details
        $return_purchaseDetail = ReturnPurchaseDetail::where('return_purchase_id', $id)->get();

        $countItems = count($return_purchaseDetail);

        // $timeStamp=now();

        for ($i = 0; $i < $countItems; $i++) {
            //get product id from each return_purchase details
            $p_id = $return_purchaseDetail[$i]['product_id'];

            $p_qty = $return_purchaseDetail[$i]['quantity'];

            //finding stock to decrease the quantity of this return_purchase
            $stock = Stock::where('store_id', $store_id)->where('product_id', $p_id);

            $stock_id = $stock->value('id');

            $stock_qty = $stock->value('quantity');

            $stock = Stock::findOrFail($stock_id);

            if ($stock_qty >= $p_qty) {

                $stock->quantity = $stock_qty - $p_qty;
            }
            if ($stock->save()) {

                if ($ReturnPurchase->delete()) {

                    return response()->json([
                        'msg' => 'successfully Deleted',
                        'status' => 'success',
                    ]);
                } else {
                    return response()->json([
                        'msg' => 'Delete Failed',
                        'status' => 'error',
                    ]);
                }
            }
        }
    }
    public function searchReturnPurchases(Request $request)
    {

        $this->authorize('hasPermission', 'search_return_purchase');

        $user = User::findOrFail(Auth::user()->id);

        $store_id = $user->stores[0]->id;

        $searchKey = $request->input('searchQuery');
        if ($searchKey != '') {
            return ReturnPurchaseResource::collection(ReturnPurchase::where('store_id', $store_id)->where('customer_name', 'like', '%' . $searchKey . '%')->get());
        } else {
            return response()->json([
                'msg' => 'Error while retriving ReturnPurchases. No Data Supplied as key.',
                'status' => 'error',
            ]);
        }
    }

    public function changeReturnPurchaseStatus(Request $request)
    {

        $this->authorize('hasPermission', 'edit_return_purchase');

        // $user = User::findOrFail(Auth::user()->id);

        // $store_id = $user->stores[0]->id;

        $key = $request->input('key');

        $value = $request->input('value');

        $return_purchase = ReturnPurchase::findOrFail($key);
        $return_purchase->status = $value;
        $return_purchase->updated_at = time();

        if ($return_purchase->save()) {
            return response()->json(['status' => 'success', 'msg' => $return_purchase->custom_return_purchase_id . ' changed to ' . $value . '']);
        } else {

            return response()->json(['status' => 'failed', 'msg' => 'Return Purchase status changed Failed']);
        }
    }
}
