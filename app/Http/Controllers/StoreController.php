<?php

namespace App\Http\Controllers;

use App\Store;
use App\User;
use Auth;
use Illuminate\Http\Request;

class StoreController extends Controller
{

    public function __construct()
    {

        $this->middleware('auth:api');

    }
    // public function index(){

    //     $user=User::findOrFail(Auth::user()->id);

    //     $store_id=$user->stores()->first()->value('id');

    //    return StoreResource::collection(Store::where('store_id',$store_id)->paginate(8));
    // }

    public function show()
    {
//later for multiple store we will pas id of the user

        $user = User::findOrFail(Auth::user()->id);

        $store_id = $user->stores[0]->id;

        $store = Store::findOrFail($store_id);

        return response()->json([
            'store'  => $store,
            'status' => 'success',
        ]);

    }

    public function selectStore()
    {

        $user = User::findOrFail(Auth::user()->id);

        $stores = $user->stores;

        //make user choose which store to load
        $msg = "You have mutliple stores. To beign with select one";

        return view('store.select', ['stores' => $stores, 'msg' => $msg]);

    }

    //not for the api
    public function selectStoreSave(Request $request)
    {

        $store_id = $request->input('selected_store_id');

        $user = User::findOrFail(Auth::user()->id);

        $hasStore = $user->hasStore($store_id); //assinging stores of logged in user

        if ($hasStore) {

            // setting session store
            session(['store_id' => $store_id]);

            return redirect()->route('dashboard');

        } else {
            print_r("404 error");
        }

    }

    public function update(Request $request)
    {

        $this->authorize('hasPermission', 'edit_store)');

        $this->validate($request, [
            'name'           => 'required|string|max:200',
            'email'          => 'required|email|max:255',
            'address'        => 'required|string|max:200',
            'url'            => 'required|string|max:200',
            'phone'          => 'required|digits:10',
            'detail'         => 'required|string|max:400',
            'tax_percentage' => 'required|numeric ',
            'tax_number'     => 'required|numeric ',
        ]);

        $id = $request->input('id');

        $user = User::findOrFail(Auth::user()->id);

        $store_id = $user->stores[0]->id;

        $store                 = Store::findOrFail($store_id);
        $store->name           = $request->input('name');
        $store->detail         = $request->input('detail');
        $store->email          = $request->input('email');
        $store->address        = $request->input('address');
        $store->phone          = $request->input('phone');
        $store->mobile         = $request->input('mobile');
        $store->url            = $request->input('company_url');
        $store->tax_percentage = $request->input('tax_percentage');
        $store->tax_number     = $request->input('tax_number');

        if ($request->hasFile('image')) {
            $imageName = time() . '.' . $request->image->getClientOriginalExtension();
            $request->image->move(public_path('img'), $imageName);
            $store->store_logo = $imageName;
        }
        if ($store->update()) {
            return response()->json([
                'msg'    => 'You have successfully changed the information.',
                'status' => 'success',
            ]);
        } else {
            return response()->json([
                'msg'    => 'Opps! My Back got cracked while working in Database',
                'status' => 'error',
            ]);
        }
    }
}
