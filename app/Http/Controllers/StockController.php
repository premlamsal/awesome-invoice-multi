<?php
namespace App\Http\Controllers;

use App\Http\Resources\Stock as StockResource;
use App\Stock;
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
        return StockResource::collection(Stock::where('store_id', $store_id)->with('product.unit')
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
        return response()
            ->json(['data' => $data]);
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
