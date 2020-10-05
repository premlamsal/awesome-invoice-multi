<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProductDetail extends Model
{
    public function product()
    {
        return $this->belongsTo('\App\Product', 'product_id', 'id');
    }

    public function unit()
    {
        return $this->belongsTo('\App\Unit', 'unit_id', 'id');
    }
    protected $fillable = ['product_id', 'action', 'action_id', 'purchase_quantity', 'sale_quantity', 'unit_id', 'price', 'in_stock'];
}
