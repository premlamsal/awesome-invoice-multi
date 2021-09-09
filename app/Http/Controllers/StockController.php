<?php
namespace App\Http\Controllers;

use App\Http\Resources\Stock as StockResource;
use App\Stock;
use App\StockAdjustment;
use App\StockHistory;
use App\User;
use Auth;
use Illuminate\Http\Request;

class StockController extends Controller
{

    public function __construct()
    {

        $this->middleware('auth:api');
    }

    public function index()
    {
        $this->authorize('hasPermission', 'view_stocks');

        $user = User::findOrFail(Auth::user()->id);

        $store_id = $user->stores[0]->id;

        // return StockResource::collection(Stock::with('product')->with('unit')->paginate(8));
        return StockResource::collection(Stock::with('product.unit')->where('store_id', $store_id)
                ->paginate(8));

        // $data= DB::table('stocks as s')
        //        ->select('s.unit_id')
        //        ->leftJoin('stocks as s1', function ($join) {
        //              $join->on('s.product_id','=','s1.product_id')
        //                   ->whereRaw(DB::raw('s.created_at < s1.created_at'));
        //         })
        //         ->join('products','s.product_id','=','products.id')
        //         ->join('units','s.unit_id','=','units.id')
        //        ->whereNull('s1.id')

        //        ->get();
        // -----------------------------------------------------------------------------
        // $data= DB::table('stocks as s')
        //     ->select('s.*','products.name','units.short_name')
        //     ->leftJoin('stocks as s1', function ($join) {
        //           $join->on('s.product_id','=','s1.product_id')
        //           ->whereRaw(DB::raw('s.id < s1.id'));
        //      })
        //     ->whereNull('s1.id')

        //      ->join('products','s.product_id','=','products.id')
        //      ->join('units','s.unit_id','=','units.id')
        //      ->paginate(8);
        // ---------------------------------------------------------------------------------

        // $data = DB::table('stocks')
        // ->select('stocks.*',)

        // ->join('units','stocks.unit_id','=','units.id')
        // ->get();

        // return $data;
        // return response()
        //     ->json(['data' => $data]);
    }

    public function destroy($id)
    {
        $this->authorize('hasPermission', 'delete_stock');

        $user = User::findOrFail(Auth::user()->id);

        $store_id = $user->stores[0]->id;

        $stock = Stock::where('id', $id)->where('store_id', $store_id)->first();
        if ($stock->delete()) {
            return response()
                ->json(['msg' => 'successfully Deleted', 'status' => 'success']);
        } else {
            return response()
                ->json(['msg' => 'Error while deleting data', 'status' => 'error']);
        }

    }

    public function show($id)
    {

        $this->authorize('hasPermission', 'show_stock');

        $user = User::findOrFail(Auth::user()->id);

        $store_id = $user->stores[0]->id;

        $stock = Stock::where('id', $id)->where('store_id', $store_id)->first();
        if ($stock->stock) {
            return response()
                ->json(['customer' => $customer, 'status' => 'success']);
        } else {
            return response()->json(['msg' => 'Error while retriving Customer', 'status' => 'error']);
        }
    }
    public function checkQuantityInStock(Request $request)
    {

        //user can't create invoice if this permission is denied
        $this->authorize('hasPermission', 'check_stock_quantity');

        $user = User::findOrFail(Auth::user()->id);

        $store_id = $user->stores[0]->id;

        $product_id = $request->input('product_id');

        $quantity = $request->input('quantity');

        $stock_quantity = Stock::where('store_id', $store_id)->where('product_id', $product_id)->value('quantity');

        if ($stock_quantity >= $quantity && $quantity > 0) {

            return response()->json(['status' => 1, 'title' => 'Info', 'msg' => 'Quantity changed.', 'quantity' => $stock_quantity]);
        } else {

            return response()->json(['status' => 0, 'title' => 'Opps!!', 'msg' => 'You have only ' . $stock_quantity . ' in stock.', 'quantity' => $stock_quantity]);
        }

    }
    public function adjustStock(Request $request)
    {

        //user can't adjust stock if this permission is denied
        $this->authorize('hasPermission', 'adjust_stock');

        $user = User::findOrFail(Auth::user()->id);

        $this->validate($request, [
            'stock_id' => 'required|numeric',
            'quantity' => 'required|numeric',
            'type' => 'required|string|max:200 ',
            'price' => 'required|numeric ',
            'date' => 'required|string|max:200 ',
            'notes' => 'required|string|max:1000 ',
            'reason' => 'required|string|max:200',
         ]);

        $store_id = $user->stores[0]->id;

        $adjusted_stock_id = $request->input('stock_id');

        $adjusted_quantity = $request->input('quantity');

        $adjusted_reason = $request->input('reason');

        $adjusted_price = $request->input('price');

        $adjusted_type = $request->input('type');

        $adjusted_date = $request->input('date');

        $adjusted_notes = $request->input('notes');

        $stock = Stock::where('store_id', $store_id)->where('id', $adjusted_stock_id)->first();
        
        $stock->price = $adjusted_price;
        
        $stock_current_quantity = $stock->value('quantity');
        
        $stock_product_id = $stock->value('product_id');


        if ($adjusted_type == 'increment') {
            
            $stock->quantity = $stock_current_quantity + $adjusted_quantity;

        } elseif ($adjusted_type == 'decrement') {
            
            $stock->quantity = $stock_current_quantity - $adjusted_quantity;
        }
        if($stock->save()){
            $stock_adjustment=new StockAdjustment();
            $stock_adjustment->store_id=$store_id;
            $stock_adjustment->product_id=$stock_product_id;
            $stock_adjustment->type=$adjusted_type;
            $stock_adjustment->stock_id=$adjusted_stock_id;
            $stock_adjustment->price=$adjusted_price;
            $stock_adjustment->reason=$adjusted_reason;
            $stock_adjustment->notes=$adjusted_notes;
            $stock_adjustment->date=$adjusted_date;
            $stock_adjustment->quantity=$adjusted_quantity;
            if($stock_adjustment->save()){
                return response()->json(['status' => 'success', 'title' => 'Wow!!', 'msg' => 'Successfully Adjusted Stock Details. Check updated details on Stock Page or Stock Histories.']);
            }
        return response()->json(['status' => 'error', 'title' => 'Opps!!', 'msg' => 'Some error occured while saving to Stock Adjustment Database. Please contact admin'], 500);

        }

        return response()->json(['status' => 'error', 'title' => 'Opps!!', 'msg' => 'Some error occured. Please contact admin'], 500);

    }

    public function showStockAdjustments($id){
        //we get stock id as 'id'
        $this->authorize('hasPermission', 'show_stock_adjustment');

        $user = User::findOrFail(Auth::user()->id);

        $store_id = $user->stores[0]->id;

        $stock_adjustment=StockAdjustment::where('store_id',$store_id)->where('stock_id',$id)->get();

        $stock=Stock::where('store_id',$store_id)->where('id',$id)->with('product')->get();

        return response()->json(['stock_adjustments'=>$stock_adjustment,'stock'=>$stock[0]]);


    }

    public function stockHistory(Request $request)
    {

        $this->authorize('hasPermission', 'stock_history');

        $user = User::findOrFail(Auth::user()->id);

        $store_id = $user->stores[0]->id;

        $dateFrom = $request->input('dateFrom');

        $dateTo = $request->input('dateTo');

        $data = StockHistory::where('store_id', $store_id)->whereBetween('date', [$dateFrom, $dateTo])->with('product')->get();

        return $data;
    }
}
