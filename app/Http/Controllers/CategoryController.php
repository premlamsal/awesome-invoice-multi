<?php

namespace App\Http\Controllers;

use App\Http\Resources\Category as CategoryResource;
use App\ProductCategory as Category;
use App\User;
use Auth;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function __construct()
    {

        $this->middleware('auth:api');

    }

    public function index()
    {

        $this->authorize('hasPermission', 'view_categories');

        $user = User::findOrFail(Auth::user()->id);

        $store_id = $user->stores[0]->id;

        return CategoryResource::collection(Category::where('store_id', $store_id)->paginate(8));
    }

    public function store(Request $request)
    {

        $this->authorize('hasPermission', 'add_category');

        $user = User::findOrFail(Auth::user()->id);

        $store_id = $user->stores[0]->id;

        $this->validate($request, [
            'name'        => 'required|string|max:10',

            'description' => 'required|string|max:100',
        ]);

        $category = new Category();

        $category->name = $request->input('name');

        $category->description = $request->input('description');

        $category->store_id = $store_id;

        if ($category->save()) {
            return response()->json(['msg' => 'You have successfully added the information.', 'status' => 'success']);
        } else {
            return response()->json(['msg' => 'Opps! My Back got cracked while working in Database', 'status' => 'error']);
        }

    }

    public function show($id)
    {

        $this->authorize('hasPermission', 'show_category');

        $user = User::findOrFail(Auth::user()->id);

        $store_id = $user->stores[0]->id;

        $category = Category::where('store_id', $store_id)->where('id', $id)->first();

        if ($category) {
            return response()->json([
                'category' => $category,
                'status'   => 'success',
            ]);
        } else {
            return response()->json(['msg' => 'Opps! My Back got cracked while working in Database', 'status' => 'error']);
        }

    }

    public function update(Request $request)
    {

        $this->authorize('hasPermission', 'edit_category');

        $user = User::findOrFail(Auth::user()->id);

        $store_id = $user->stores[0]->id;

        $this->validate($request, [
            'name'        => 'required|string|max:10',
            'description' => 'required|string|max:100',
        ]);

        $id = $request->input('id');

        $category = Category::where('store_id', $store_id)->where('id', $id)->first();

        $category->name = $request->input('name');

        $category->description = $request->input('description');

        if ($category->save()) {
            return response()->json([
                'msg'    => "Record Updated successfully",
                'status' => 'success',
            ]);
        } else {
            return response()->json([
                'msg'    => 'Error Updating Data',
                'status' => 'error',
            ]);
        }
    }

    public function destroy($id)
    {
        $this->authorize('hasPermission', 'delete_category');

        $user = User::findOrFail(Auth::user()->id);

        $store_id = $user->stores[0]->id;

        $category = Category::where('store_id', $store_id)->where('id', $id)->first();

        if ($category->delete()) {
            return response()->json([
                'msg'    => 'successfully Deleted',
                'status' => 'success',
            ]);
        } else {
            return response()->json([
                'msg'    => 'Error while deleting data',
                'status' => 'error',
            ]);
        }
    }

    public function searchCategories(Request $request)
    {

        $this->authorize('hasPermission', 'search_category');

        $searchKey = $request->input('searchQuery');

        $user = User::findOrFail(Auth::user()->id);
        $store_id = $user->stores[0]->id;

        if ($searchKey != '') {
            return CategoryResource::collection(Category::where('store_id', $store_id)->where('name', 'like', '%' . $searchKey . '%')->paginate(8));
        } else {
            return response()->json([
                'msg'    => 'Error while retriving Categories. No Data Supplied as key.',
                'status' => 'error',
            ]);
        }
    }
}
