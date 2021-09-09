<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StockAdjustment extends Model
{
    public function product()
    {
        return $this->belongsTo('\App\Product', 'product_id', 'id');
    }

    public function stock()
    {
        return $this->belongsTo('\App\Stock', 'stock_id', 'id');
    }
}
