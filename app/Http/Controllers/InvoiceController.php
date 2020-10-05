<?php

namespace App\Http\Controllers;

use App\Http\Resources\Invoice as InvoiceResource;
use App\Invoice;
use App\InvoiceDetail;
use App\Stock;
use App\StockHistory;
use App\Store;
use App\User;
use Auth;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{

    public function __construct()
    {

        $this->middleware('auth:api');

    }
    public function index()
    {
        /*---------------------------------------------------------
        This block will only return non-realtionship model

        // Get Invoices
        // $Invoices= Invoice::orderBy('created_at', 'desc')->paginate(3);

        //Return collection of Invoices as a resource
        // return InvoiceResource::collection($Invoices);
        -----------------------------------------------------------*/

        $this->authorize('hasPermission', 'view_invoices');

        $user = User::findOrFail(Auth::user()->id);

        $store_id = $user->stores[0]->id;

        return InvoiceResource::collection(Invoice::where('store_id', $store_id)->with('invoiceDetail')->orderBy('updated_at', 'desc')->paginate(8));
    }

    public function store(Request $request)
    {
        $this->authorize('hasPermission', 'add_invoice');

        $user = User::findOrFail(Auth::user()->id);

        $store_id = $user->stores[0]->id;

        // //validation
        $this->validate($request, [

            'info.note'            => 'required | string |max:200',
            'info.customer_name'   => 'required | string| max:200',
            'info.due_date'        => 'required | date',
            'info.invoice_date'    => 'required | date',

            'info.discount'        => 'required | numeric| max:200',

            'items.*.product_name' => 'required | string |max:200',
            'items.*.price'        => 'required | numeric',
            'items.*.quantity'     => 'required | numeric',

        ]);

        // $settings=Setting::findOrFail(1);

        $store = Store::findOrFail($store_id);

        $store_tax_percentage = $store->tax_percentage;

        $store_tax = $store_tax_percentage / 100;

        //old invoice id
        $invoice_id_count = $store->invoice_id_count;

        //explode invoice id from database

        $custom_invoice_id = explode('-', $invoice_id_count);

        $custom_invoice_id[1] = $custom_invoice_id[1] + 1; //increase invoice

        //new custom_invoice_id
        $new_count_invoice_id = implode('-', $custom_invoice_id);

        //collecting data
        $items = collect($request->items)->transform(function ($item) {
            $item['line_total'] = $item['quantity'] * $item['price'];
            return new InvoiceDetail($item);
        });

        if ($items->isEmpty()) {
            return response()
                ->json([
                    'items_empty' => 'One or more Item is required.',
                ], 422);
        }

        $data                      = $request->info;
        $data['sub_total']         = $items->sum('line_total');
        $data['tax_amount']        = $data['sub_total'] * $store_tax;
        $data['grand_total']       = $data['sub_total'] + $data['tax_amount'] - $data['discount'];
        $data['store_id']          = $store_id;
        $data['custom_invoice_id'] = $new_count_invoice_id;

        $invoice = Invoice::create($data);

        $invoice->invoiceDetail()->saveMany($items);

        //for inserting in stock and altering if already has one initialized stock and previous stock
        $items = collect($request->items);

        $countItems = count($items);

        $timeStamp = now();

        $jsonResponse = array();

        for ($i = 0; $i < $countItems; $i++) {

            $p_id = $items[$i]['product_id'];

            $stock = Stock::where('product_id', $p_id)->where('store_id', $store_id);

            //retirving current product-> stock quantity
            $in_stock_quantity = $stock->value('quantity');

            //get stock id
            $stock_id = $stock->value('id');

            if ($in_stock_quantity >= $items[$i]['quantity'] && $items[$i]['quantity'] > 0) {

                //adding current stock with new purchased product quantity
                $new_stock_quantity = $in_stock_quantity - $items[$i]['quantity'];

                $stock = Stock::where('store_id', $store_id)->first();

                $stock->quantity = $new_stock_quantity;

                $stock->unit_id = $items[$i]['unit_id'];

                $stock->created_at = $timeStamp;

                $stock->updated_at = $timeStamp;

                if ($stock->save()) {

                    // $today = date('Y-m-d');
                    $date_stock_histroy = $data['invoice_date'];

                    $StockHistoryID = StockHistory::where('store_id', $store_id)->where('date', '=', $date_stock_histroy)->where('product_id', $p_id)->value('id');

                    if ($StockHistoryID != null) {

                        $StockHistory = StockHistory::where('store_id', $store_id)->where('id', $StockHistoryID)->first();

                        $StockHistory->quantity = $new_stock_quantity;

                        $StockHistory->store_id = $store_id;

                        if ($StockHistory->save()) {

                            //set current invoice_id_count to store table
                            $store->invoice_id_count = $new_count_invoice_id;
                            if ($store->save()) {
                                $jsonResponse = ['msg' => 'You have successfully created the Invoice.', 'status' => 'success', 'id' => $invoice_id_count];

                            }

                        }

                    } else {
                        $StockHistory = new StockHistory();

                        $StockHistory->product_id = $p_id;

                        $StockHistory->quantity = $new_stock_quantity;

                        // $StockHistory->date = $today;

                        $StockHistory->date = $date_stock_histroy;

                        $StockHistory->store_id = $store_id;

                        if ($StockHistory->save()) {

                            //set current invoice_id_count to store table
                            $store->invoice_id_count = $new_count_invoice_id;

                            if ($store->save()) {
                                $jsonResponse = ['msg' => 'You have successfully created the Invoice.', 'status' => 'success', 'id' => $invoice_id_count];

                            }
                        }
                    }
                } else {

                    $jsonResponse = ['msg' => 'Failed Saving the Data to the Stock.', 'status' => 'error'];

                }

            } else {
                $jsonResponse = ['msg' => 'You dont have Stock.', 'status' => 'error'];
            }

        }

        return response()->json($jsonResponse);

    }
    public function update(Request $request)
    {

        $this->authorize('hasPermission', 'edit_invoice');

        $user = User::findOrFail(Auth::user()->id);

        $store_id = $user->stores[0]->id;
        // //validation
        $this->validate($request, [

            'info.note'          => 'required | string |max:200',
            'info.customer_name' => 'required | string| max:200',
            'info.due_date'      => 'required | date',
            'info.invoice_date'  => 'required | date',

            // 'info.discount' => 'required | numeric| max:200',

            // 'items.*.product_name' => 'required | string |max:200',
            // 'items.*.price' => 'required | numeric',
            // 'items.*.quantity' => 'required | numeric',

        ]);
        $id = $request->id;

        $invoice = Invoice::where('id', $id)->where('store_id', $store_id)->first();

        // $items = collect($request->items)->transform(function($item) {
        //     $item['line_total'] = $item['quantity'] *$item['price'];
        //     return new InvoiceDetail($item);
        // });

        // if($items->isEmpty()) {
        //     return response()
        //     ->json([
        //         'items_empty' => ['One or more Item is required.']
        //     ], 422);
        // }

        $store                = Store::findOrFail($store_id);
        $store_tax_percentage = $store->tax_percentage;

        $store_tax = $store_tax_percentage / 100;

        $data = $request->info;

        // $data['sub_total'] = $items->sum('line_total');
        // $data['tax_amount'] = $data['sub_total'] * $store_tax;
        // $data['grand_total'] = $data['sub_total'] + $data['tax_amount'] - $data['discount'];
        // $data['store_id']=$store_id;

        $invoice->update($data);

        // InvoiceDetail::where('invoice_id', $invoice->id)->delete();

        // $invoice->invoiceDetail()->saveMany($items);

        return response()->json(['msg' => 'You have successfully updated the Invoice.', 'status' => 'success']);

    }

    public function returnInvoice(Request $request)
    {

        $this->authorize('hasPermission', 'return_invoice');

        $user = User::findOrFail(Auth::user()->id);

        $store_id = $user->stores[0]->id;
        // //validation
        $this->validate($request, [

            'info.note'            => 'required | string |max:200',
            'info.supplier_name'   => 'required | string| max:200',
            'info.due_date'        => 'required | date',
            'info.invoice_date'    => 'required | date',

            'info.discount'        => 'required | numeric| max:200',

            'items.*.product_name' => 'required | string |max:200',
            'items.*.price'        => 'required | numeric',
            'items.*.quantity'     => 'required | numeric',

        ]);

        $id = $request->id; //invoice id

        $invoice = Invoice::where('id', $id)->where('store_id', $store_id)->first();

        $items = collect($request->items)->transform(function ($item) {
            $item['line_total'] = $item['quantity'] * $item['price'];
            return new InvoiceDetail($item);
        });

        $store                = Store::findOrFail($store_id);
        $store_tax_percentage = $store->tax_percentage;

        $store_tax = $store_tax_percentage / 100;

        if ($items->isEmpty()) {
            return response()
                ->json([
                    'items_empty' => ['One or more Item is required.'],
                ], 422);
        }

        $data = $request->info;

        $data['sub_total']   = $items->sum('line_total');
        $data['tax_amount']  = $data['sub_total'] * $store_tax;
        $data['grand_total'] = $data['sub_total'] + $data['tax_amount'] - $data['discount'];
        $data['store_id']    = $store_id;

        //for inserting in stock and altering if already has one initialized stock and previous stock
        $items_raw = collect($request->items); //collecting new items from the submit form

        $countItemsNew = count($items_raw); //get new items length of elements

        $timeStamp = now();

        //retriving old invoice records for the references
        $invoiceDetail_old = InvoiceDetail::where('invoice_id', $id)->get(); //get old data from the database

        $countItemsOld = count($invoiceDetail_old); //get old items length of elements

        for ($i = 0; $i < $countItemsOld; $i++) {

            $p_id = $items[$i]['product_id'];

            $stock = Stock::where('product_id', $p_id);

            //retirving current product-> stock quantity
            $in_stock_quantity = $stock->value('quantity');

            //get stock id
            $stock_id = $stock->value('id');

            //adding current stock with new purchased product quantity
            if ($in_stock_quantity >= $items[$i]['quantity']) {

                $new_stock_quantity = $in_stock_quantity - $items[$i]['quantity'];

                $stock = Stock::where('store_id', $store_id)->where('id', $stock_id)->first();

                $stock->quantity = $new_stock_quantity;

                $stock->unit_id = $items[$i]['unit_id'];

                $stock->created_at = $timeStamp;

                $stock->updated_at = $timeStamp;

                if ($stock->save()) {

                    $invoice->update($data);

                    InvoiceDetail::where('invoice_id', $invoice->id)->delete();

                    $invoice->InvoiceDetail()->saveMany($items);

                    return response()->json(['msg' => 'You have successfully return the invoice.', 'status' => 'success']);

                }
            }
        }
        return response()->json(['msg' => 'Failed while returning invoice. Check your stock quanity.', 'status' => 'error']);
    }

    public function show($id)
    {
        $this->authorize('hasPermission', 'show_invoice');

        $user = User::findOrFail(Auth::user()->id);

        $store_id = $user->stores[0]->id;
        // Get Invoice
        $Invoice = Invoice::where('store_id', $store_id)->with('invoiceDetail.product.unit')->with('customer')->findOrFail($id);

        // $customer_id=$Invoice->customer_id;
        // $Customer=Customer::findOrFail($customer_id);

        return response()
            ->json([
                'invoice' => $Invoice,
                // 'customer' => $Customer
            ]);

    }

    public function destroy($id)
    {
        $this->authorize('hasPermission', 'delete_invoice');

        $user = User::findOrFail(Auth::user()->id);

        $store_id = $user->stores[0]->id;

        // Get Invoice
        $Invoice = Invoice::where('id', $id)->where('store_id', $store_id)->first();

        //get invoice details
        $invoiceDetail = InvoiceDetail::where('invoice_id', $id)->get();

        $countItems = count($invoiceDetail);

        // $timeStamp=now();
        if ($countItems != 0) {

            for ($i = 0; $i < $countItems; $i++) {
                //get product id from each invoice details
                $p_id = $invoiceDetail[$i]['product_id'];

                $p_qty = $invoiceDetail[$i]['quantity'];

                //finding stock to decrease the quantity of this invoice
                $stock = Stock::where('product_id', $p_id)->where('store_id', $store_id);

                $stock_id = $stock->value('id');

                $stock_qty = $stock->value('quantity');

                $stock = Stock::where('id', $stock_id)->where('store_id', $store_id)->first();

                if ($stock_qty >= 0) {

                    $stock->quantity = $stock_qty + $p_qty;

                }
                if ($stock->save()) {

                    if ($Invoice->delete()) {

                        return response()->json([
                            'msg'    => 'successfully Deleted',
                            'status' => 'success',
                        ]);
                    } else {
                        return response()->json([
                            'msg'    => 'Delete Failed',
                            'status' => 'error',
                        ]);
                    }
                }

            }

        } else {

            if ($Invoice->delete()) {

                return response()->json([
                    'msg'    => 'Successfully Deleted',
                    'status' => 'success',
                ]);
            } else {
                return response()->json([
                    'msg'    => 'Delete Failed',
                    'status' => 'error',
                ]);
            }
        }

    }
    public function searchInvoices(Request $request)
    {

        $this->authorize('hasPermission', 'search_invoice');

        $user = User::findOrFail(Auth::user()->id);

        $store_id = $user->stores[0]->id;

        $searchKey = $request->input('searchQuery');
        if ($searchKey != '') {

            // $queryResults=Estimate::where('customer_name','like','%'.$searchQuery.'%')->get();
            return InvoiceResource::collection(Invoice::where('store_id', $store_id)->where('customer_name', 'like', '%' . $searchKey . '%')->paginate(8));
        } else {
            return response()->json([
                'msg'    => 'Error while retriving Invoices. No Data Supplied as key.',
                'status' => 'error',
            ]);
        }
    }

}
