<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StockDetail extends Model
{
    public function stock()
    {
        return $this->belongsTo('\App\Stock', 'stock_id', 'id');
    }

    public function product()
    {
        return $this->belongsTo('\App\Product', 'product_id', 'id');
    }

    public function unit()
    {
        return $this->belongsTo('\App\Unit', 'unit_id', 'id');
    }
    protected $fillable = ['stock_id', 'action', 'action_id', 'purchase_quantity', 'sale_quantity', 'unit_id', 'price', 'in_stock'];
}
