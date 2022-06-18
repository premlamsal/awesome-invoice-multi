<?php

namespace App\Http\Controllers;

use App\CustomerTransaction;
use App\Http\Resources\ReturnInvoice as ReturnInvoiceResource;
use App\ReturnInvoice;
use App\ReturnInvoiceDetail;
use App\Stock;
use App\Store;
use App\User;
use Auth;
use Illuminate\Http\Request;

class ReturnInvoiceController extends Controller
{
    public function __construct()
    {

        $this->middleware('auth:api');
    }
    public function index()
    {
        /*---------------------------------------------------------
        This block will only return non-realtionship model

        // Get ReturnInvoices
        // $ReturnInvoices= ReturnInvoice::orderBy('created_at', 'desc')->paginate(3);

        //Return collection of ReturnInvoices as a resource
        // return ReturnInvoiceResource::collection($ReturnInvoices);
        -----------------------------------------------------------*/

        $this->authorize('hasPermission', 'view_return_invoices');

        $user = User::findOrFail(Auth::user()->id);

        $store_id = $user->stores[0]->id;

        return ReturnInvoiceResource::collection(ReturnInvoice::where('store_id', $store_id)->with('returnInvoiceDetail')->orderBy('updated_at', 'desc')->paginate(8));
    }

    public function store(Request $request)
    {
        $return_invoice_status_save = false;

        $this->authorize('hasPermission', 'add_return_invoice');

        $user = User::findOrFail(Auth::user()->id);

        $store_id = $user->stores[0]->id;

        // //validation
        $this->validate($request, [

            'info.note' => 'required | string |max:200',
            'info.customer_name' => 'required | string| max:200',
            'info.customer_id' => 'required',
            'info.due_date' => 'required | date',
            'info.return_invoice_date' => 'required | date',
            'info.customer_id' => 'required',
            'info.discount' => 'required | numeric| max:200',

            'items.*.product_name' => 'required | string |max:200',
            'items.*.price' => 'required | numeric',
            'items.*.quantity' => 'required | numeric',

        ]);

        // $settings=Setting::findOrFail(1);

        $store = Store::findOrFail($store_id);

        $store_tax_percentage = $store->tax_percentage;

        $store_tax = $store_tax_percentage / 100;

        //old return_invoice id
        $return_invoice_id_count = $store->return_invoice_id_count;

        //explode return_invoice id from database

        $custom_return_invoice_id = explode('-', $return_invoice_id_count);

        $custom_return_invoice_id[1] = $custom_return_invoice_id[1] + 1; //increase return_invoice

        //new custom_return_invoice_id
        $new_count_return_invoice_id = implode('-', $custom_return_invoice_id);

        //collecting data
        $items = collect($request->items)->transform(function ($item) {
            $item['line_total'] = $item['quantity'] * $item['price'];
            return new ReturnInvoiceDetail($item);
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
        $data['custom_return_invoice_id'] = $new_count_return_invoice_id;

        //transaction started

        $return_invoice = ReturnInvoice::create($data);

        $return_invoice->returnInvoiceDetail()->saveMany($items);

        //for inserting in stock and altering if already has one initialized stock and previous stock
        $items = collect($request->items);

        $countItems = count($items);

        $timeStamp = now();

        $jsonResponse = array();

        for ($i = 0; $i < $countItems; $i++) {

            $p_id = $items[$i]['product_id'];

            $stock_id = $items[$i]['stock_id'];

            $stock = Stock::where('id', $stock_id)->where('product_id', $p_id)->where('store_id', $store_id);

            //retirving current product-> stock quantity
            $in_stock_quantity = $stock->value('quantity');

            //get stock id //instead we already have stock_id but picking from quered varible.
            $stock_id = $stock->value('id');

            if ($in_stock_quantity >= $items[$i]['quantity'] && $items[$i]['quantity'] > 0) {

                //adding current stock with new purchased product quantity
                $new_stock_quantity = $in_stock_quantity + $items[$i]['quantity'];

                $stock = Stock::findOrFail($stock_id);

                $stock->quantity = $new_stock_quantity;

                $stock->unit_id = $items[$i]['unit_id'];

                $stock->created_at = $timeStamp;

                $stock->updated_at = $timeStamp;

                if ($stock->save()) {

                    //set current return_invoice_id_count to store table
                    $store->return_invoice_id_count = $new_count_return_invoice_id;

                    if ($store->save()) {

                        $return_invoice_status_save = true;
                    } else {

                        $jsonResponse = ['msg' => 'Failed updating the Data to the store.', 'status' => 'error'];
                    }
                } else {

                    $jsonResponse = ['msg' => 'Failed Saving the Data to the Stock.', 'status' => 'error'];
                }
            } else {
                $jsonResponse = ['msg' => 'You dont have Stock.', 'status' => 'error'];
            }
        }
        if ($return_invoice_status_save) {
            $CustomerTransaction = new CustomerTransaction();
            $CustomerTransaction->transaction_type = "sales_return";
            $CustomerTransaction->refId = $return_invoice->id;
            $CustomerTransaction->amount = $data['grand_total'];
            $CustomerTransaction->customer_id = $data['customer_id'];
            $CustomerTransaction->store_id = $data['store_id'];
            $CustomerTransaction->date = $data['return_invoice_date'];
            if ($CustomerTransaction->save()) {
                $jsonResponse = ['msg' => 'Successfully created return invoice & customer transactions', 'status' => 'success'];
            } else {

                $jsonResponse = ['msg' => 'Error adding return invoice to customer transaction.', 'status' => 'error'];
            }
        }
        return response()->json($jsonResponse);
    }
    public function update(Request $request)
    {

        $this->authorize('hasPermission', 'edit_return_invoice');

        $user = User::findOrFail(Auth::user()->id);

        $store_id = $user->stores[0]->id;
        // //validation
        $this->validate($request, [

            'info.note' => 'required | string |max:200',
            'info.customer_name' => 'required | string| max:200',
            'info.customer_id' => 'required',
            'info.due_date' => 'required | date',
            'info.return_invoice_date' => 'required | date',

            'info.discount' => 'required | numeric| max:200',

            'items.*.product_name' => 'required | string |max:200',
            'items.*.price' => 'required | numeric',
            'items.*.quantity' => 'required | numeric',

        ]);
        $id = $request->id; //we will get return_invoice id here

        $return_invoice = ReturnInvoice::where('id', $id)->where('store_id', $store_id)->first();

        $items = collect($request->items)->transform(function ($item) {
            $item['line_total'] = $item['quantity'] * $item['price'];
            return new ReturnInvoiceDetail($item);
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
        // Get ReturnInvoice
        $ReturnInvoice = ReturnInvoice::where('id', $id)->where('store_id', $store_id)->first();

        //get return_invoice details
        $return_invoiceDetail = ReturnInvoiceDetail::where('return_invoice_id', $id)->get();

        $countItems = count($return_invoiceDetail);

        $check_save_stock = false;

        // $timeStamp=now();
        if ($countItems != 0) {

            for ($i = 0; $i < $countItems; $i++) {
                //get product id from each return_invoice details
                $p_id = $return_invoiceDetail[$i]['product_id'];

                $old_return_invoice_detail_qty = $return_invoiceDetail[$i]['quantity'];

                //finding stock to decrease the quantity of this return_invoice
                $stock = Stock::where('product_id', $p_id)->where('store_id', $store_id);

                $stock_id = $stock->value('id');

                $stock_qty = $stock->value('quantity');

                $old_stock_qty = $stock_qty + $old_return_invoice_detail_qty;

                $stock = Stock::where('id', $stock_id)->where('store_id', $store_id)->first();

                $stock->quantity = $old_stock_qty - $items[$i]['quantity'];

                if ($stock->save()) {
                    $check_save_stock = true;
                } else {
                    $check_save_stock = false;
                }
            }
            if ($check_save_stock) {

                $return_invoice->update($data);

                ReturnInvoiceDetail::where('return_invoice_id', $return_invoice->id)->delete();

                $return_invoice->returnInvoiceDetail()->saveMany($items);

                $CustomerTransaction = CustomerTransaction::where('refID', $return_invoice->id)->where('store_id', $store_id)->first();
                // $CustomerTransaction->transaction_type = "sales";
                // $CustomerTransaction->refId = $return_invoice->id;
                $CustomerTransaction->amount = $data['grand_total'];
                $CustomerTransaction->customer_id = $data['customer_id'];
                // $CustomerTransaction->store_id = $data['store_id'];
                $CustomerTransaction->date = $data['return_invoice_date'];
                if ($CustomerTransaction->save()) {
                    return response()->json(['msg' => 'You have successfully updated the ReturnInvoice.', 'status' => 'success']);
                } else {
                    return response()->json(['msg' => 'Error adding return_invoice to customer transaction.', 'status' => 'success'], 500);
                }
            } else {
                //saving stock fails
                return response()->json(['msg' => 'Initial update to stock failed.', 'status' => 'error'], 500);
            }

            // check stock save status and do following

        } else {

            return response()->json([
                'msg' => 'Update Failed. There is no items in this return_invoice',
                'status' => 'error',
            ], 500);
        }
    }

    public function show($id)
    {
        $this->authorize('hasPermission', 'show_return_invoice');

        $user = User::findOrFail(Auth::user()->id);

        $store_id = $user->stores[0]->id;
        // Get ReturnInvoice
        $ReturnInvoice = ReturnInvoice::where('store_id', $store_id)->with('returnInvoiceDetail.product.unit')->with('customer')->findOrFail($id);

        // $customer_id=$ReturnInvoice->customer_id;
        // $Customer=Customer::findOrFail($customer_id);

        return response()
            ->json([
                'return_invoice' => $ReturnInvoice,
                // 'customer' => $Customer
            ]);
    }

    public function destroy($id)
    {
        $this->authorize('hasPermission', 'delete_return_invoice');

        $user = User::findOrFail(Auth::user()->id);

        $store_id = $user->stores[0]->id;

        // Get ReturnInvoice
        $ReturnInvoice = ReturnInvoice::where('id', $id)->where('store_id', $store_id)->first();

        //get return_invoice details
        $return_invoiceDetail = ReturnInvoiceDetail::where('return_invoice_id', $id)->get();

        $countItems = count($return_invoiceDetail);

        // $timeStamp=now();
        if ($countItems != 0) {

            for ($i = 0; $i < $countItems; $i++) {
                //get product id from each return_invoice details
                $p_id = $return_invoiceDetail[$i]['product_id'];

                $p_qty = $return_invoiceDetail[$i]['quantity'];

                //finding stock to decrease the quantity of this return_invoice
                $stock = Stock::where('product_id', $p_id)->where('store_id', $store_id);

                $stock_id = $stock->value('id');

                $stock_qty = $stock->value('quantity');

                $stock = Stock::where('id', $stock_id)->where('store_id', $store_id)->first();

                if ($stock_qty >= 0) {

                    $stock->quantity = $stock_qty - $p_qty;
                }
                if ($stock->save()) {

                    if ($ReturnInvoice->delete()) {

                        $CustomerTransaction = CustomerTransaction::where('refID', $ReturnInvoice->id)->where('store_id', $store_id)->first();
                        if ($CustomerTransaction->delete()) {
                            return response()->json([
                                'msg' => 'successfully Deleted',
                                'status' => 'success',
                            ]);
                        } else {
                            return response()->json([
                                'msg' => 'Customer Transaction Delete Failed',
                                'status' => 'error',
                            ]);
                        }
                    } else {
                        return response()->json([
                            'msg' => 'ReturnInvoice Delete Failed',
                            'status' => 'error',
                        ]);
                    }
                }
            }
        } else {

            if ($ReturnInvoice->delete()) {

                return response()->json([
                    'msg' => 'Successfully Deleted',
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
    public function searchReturnInvoices(Request $request)
    {

        $this->authorize('hasPermission', 'search_return_invoice');

        $user = User::findOrFail(Auth::user()->id);

        $store_id = $user->stores[0]->id;

        $searchKey = $request->input('searchQuery');
        if ($searchKey != '') {

            // $queryResults=Estimate::where('customer_name','like','%'.$searchQuery.'%')->get();
            return ReturnInvoiceResource::collection(ReturnInvoice::where('store_id', $store_id)->where('customer_name', 'like', '%' . $searchKey . '%')->paginate(8));
        } else {
            return response()->json([
                'msg' => 'Error while retriving ReturnInvoices. No Data Supplied as key.',
                'status' => 'error',
            ]);
        }
    }

    public function changeReturnInvoiceStatus(Request $request)
    {

        $this->authorize('hasPermission', 'edit_return_invoice');

        // $user = User::findOrFail(Auth::user()->id);

        // $store_id = $user->stores[0]->id;

        $key = $request->input('key');

        $value = $request->input('value');

        $return_invoice = ReturnInvoice::findOrFail($key);
        $return_invoice->status = $value;
        $return_invoice->updated_at = time();

        if ($return_invoice->save()) {
            return response()->json(['status' => 'success', 'msg' => $return_invoice->custom_return_invoice_id . ' changed to ' . $value . '']);
        } else {

            return response()->json(['status' => 'failed', 'msg' => 'ReturnInvoice status changed Failed']);
        }
    }
}
