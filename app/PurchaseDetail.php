<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PurchaseDetail extends Model
{
    public function purchase()
    {
        return $this->belongsTo('\App\Purchase', 'purchase_id', 'id');
    }
    public function unit()
    {
        return $this->belongsTo('\App\Unit', 'unit_id', 'id');
    }
    public function product()
    {
        return $this->belongsTo('\App\Product', 'product_id', 'id');
    }

    protected $fillable = ['purchase_id', 'product_id', 'product_name', 'quantity', 'unit_id', 'price', 'line_total'];
}
