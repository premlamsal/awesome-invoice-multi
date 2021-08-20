<?php

namespace App\Http\Controllers;

use App\Http\Resources\Product as ProductResource;
use App\Product;
use App\Stock;
use App\Store;
use App\User;
use Auth;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function __construct()
    {

        $this->middleware('auth:api');

    }
    public function index()
    {

        $this->authorize('hasPermission', 'view_products');

        $user = User::findOrFail(Auth::user()->id);

        $store_id = $user->stores[0]->id;

        return ProductResource::collection(Product::where('store_id', $store_id)->with('unit')->with('category')->paginate(8));
    }

    public function store(Request $request)
    {

        // $this->authorize('hasPermission','add_product');

        $user = User::findOrFail(Auth::user()->id);

        $store_id = $user->stores[0]->id;

        $this->validate($request, [
            'name' => 'required|string|max:200',
            'description' => 'required|string|max:1000',
            'cp' => 'required|numeric ',
            'sp' => 'required|numeric ',
            'product_cat_id' => 'required|numeric ',
            'unit_id' => 'required|numeric ',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:4048',

        ]);

        //getting current custom product id from respective store

        $store = Store::findOrFail($store_id);
        //old product id
        $product_id_count = $store->product_id_count;

        //explode product id from database

        $custom_product_id = explode('-', $product_id_count);

        $custom_product_id[1] = $custom_product_id[1] + 1; //increase product

        //new custom_product_id
        $new_count_product_id = implode('-', $custom_product_id);

        $product = new Product();
        $product->name = $request->input('name');
        $product->product_cat_id = $request->input('product_cat_id');
        $product->unit_id = $request->input('unit_id');
        $product->description = $request->input('description');
        $product->cp = $request->input('cp');
        $product->sp = $request->input('sp');
        $product->opening_stock = $request->input('opening_stock');
        $product->store_id = $store_id;
        $product->custom_product_id = $new_count_product_id; //asign new increase product custom id

        $product->store_id = $store_id;

        if ($request->hasFile('image')) {
            $imageName = '/img/' . time() . '.' . $request->image->getClientOriginalExtension();
            $request->image->move(public_path('img'), $imageName);
            $product->image = $imageName;
        }
        if ($product->save()) {

            $stock = new Stock();
            if (($request->input('opening_stock')) > 0) {
                $stock->quantity = $request->input('opening_stock');
            } else {
                $stock->quantity = 0.00;
            }
            $stock->product_id = $product->id;
            
            $stock->unit_id = $request->input('unit_id');

            $stock->store_id = $store_id;

            $stock->price = $request->input('cp');

            if ($stock->save()) {
                //set current product_id_count to store table
                $store->product_id_count = $new_count_product_id;

                if ($store->save()) {

                    return response()->json([
                        'msg' => 'You have successfully changed the information.',
                        'status' => 'success',
                    ]);
                } else {
                    return response()->json([
                        'msg' => 'Error saving data to store.',
                        'status' => 'error',
                    ]);
                }

            } else {
                return response()->json([
                    'msg' => 'Error saving data to stock.',
                    'status' => 'error',
                ]);
            }

        } else {
            return response()->json([
                'msg' => 'Opps! My Back got cracked while working in Database',
                'status' => 'error',
            ]);
        }

    }
    public function update(Request $request)
    {

        $this->authorize('hasPermission', 'edit_product');

        $user = User::findOrFail(Auth::user()->id);

        $store_id = $user->stores[0]->id;

        $this->validate($request, [
            'name' => 'required|string|max:200',
            'description' => 'required|string|max:1000',
            'cp' => 'required|numeric ',
            'sp' => 'required|numeric ',
            'product_cat_id' => 'required|numeric ',
            'unit_id' => 'required|numeric ',
            'id' => 'required|numeric ',
            // 'image'=> 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048'

        ]);

        $id = $request->input('id');
        $product = Product::where('id', $id)->where('store_id', $store_id)->first();
        $product->name = $request->input('name');
        $product->product_cat_id = $request->input('product_cat_id');
        $product->unit_id = $request->input('unit_id');
        $product->description = $request->input('description');
        // $product->cp = $request->input('cp');
        // $product->sp = $request->input('sp');

        if ($request->hasFile('image')) {

            $img_ext = $request->image->getClientOriginalExtension();

            $checkExt = array("jpg", "png", "jpeg");

            if (in_array($img_ext, $checkExt)) {

                $imageName = '/img/' . time() . '.' . $request->image->getClientOriginalExtension();
                $request->image->move(public_path('img'), $imageName);
                $product->image = $imageName;

            } else {
                return response()->json([
                    'msg' => 'Opps! My Back got cracked while working in Database',
                    'status' => 'error',
                ]);
            }

        }
        if ($product->update()) {
            return response()->json([
                'msg' => 'You have successfully changed the information.',
                'status' => 'success',
            ]);
        } else {
            return response()->json([
                'msg' => 'Opps! My Back got cracked while working in Database',
                'status' => 'error',
            ]);
        }

    }

    public function show($id)
    {

        $this->authorize('hasPermission', 'show_product');

        $user = User::findOrFail(Auth::user()->id);

        $store_id = $user->stores[0]->id;

        $product = Product::where('id', $id)->where('store_id', $store_id)->with('category')->with('unit')->first();

        if ($product) {
            return response()->json([
                'product' => $product,
                'status' => 'success',
            ]);
        } else {
            return response()->json(['msg' => 'Opps! My Back got cracked while working in Database', 'status' => 'error']);
        }
    }

    public function destroy($id)
    {
        $this->authorize('hasPermission', 'delete_product');

        $user = User::findOrFail(Auth::user()->id);

        $store_id = $user->stores[0]->id;

        $product = Product::where('id', $id)->where('store_id', $store_id)->first();
        if ($product->delete()) {
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

    public function searchProducts(Request $request)
    {

        $this->authorize('hasPermission', 'search_product');

        $user = User::findOrFail(Auth::user()->id);

        $store_id = $user->stores[0]->id;

        $searchKey = $request->input('searchQuery');
        if ($searchKey != '') {
            $product=Product::where('name', 'like', '%' . $searchKey . '%')->where('store_id', $store_id)->with('unit')->with('category')->get();
            $product_suggestion=array();
            for($i=0;$i<$product->count();$i++){
                $temp=array();
                $temp=Stock::where('product_id',$product[$i]->id)->with('product.unit')->get();
                $product_suggestion[$i]=$temp;
            }
            return response()->json([
                'data' => $product_suggestion[0],
            ]);
        
        } else {
            return response()->json([
                'msg' => 'Error while retriving Products. No Data Supplied as key.',
                'status' => 'error',
            ]);
        }

    }

}
