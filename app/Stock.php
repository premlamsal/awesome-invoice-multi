<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Stock extends Model
{

    public function index()
    {

        $this->authorize('isAdmin'); //only admin will access this function

        return UserResource::collection(User::paginate(8));

    }

    public function product()
    {
        return $this->belongsTo('\App\Product', 'product_id', 'id');
    }

    protected $fillable = ['product_id', 'quantity', 'unit_id', 'price'];
}
