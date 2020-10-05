<?php

namespace App\Http\Controllers;

use App\Http\Resources\StockDetail as StockDetailResource;
use App\StockDetail;

class StockDetailController extends Controller
{
    public function __construct()
    {

        $this->middleware('auth:api');

    }

    public function show($id)
    {

        return StockDetailResource::collection(StockDetail::where('product_id', $id)->with('product')->with('unit')->paginate(8));
    }
}
