<?php

namespace App\Http\Controllers;

use App\User;
use Auth;

class HomeController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified']);
    }

    public function dashboard()
    {

        $user = User::findOrFail(Auth::user()->id);

        $stores = $user->stores();

        $count_store = count($stores->get());

        if ($count_store == 0) {

            $msg = "You do not have any Store. Add one to stat with.";

            return view('store.create', ['msg' => $msg]);
        }

        $store_id = $user->stores[0]->id;

        return view('dashboard');
    }
}
