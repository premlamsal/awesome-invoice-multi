<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ReturnInvoiceDetail extends Model
{
    public function returnInvoice()
    {
        return $this->belongsTo('\App\ReturnInvoice', 'return_invoice_id', 'id');
    }

    public function unit()
    {
        return $this->belongsTo('\App\Unit', 'unit_id', 'id');
    }

    public function product()
    {
        return $this->belongsTo('\App\Product', 'product_id', 'id');
    }
    
    protected $fillable = ['return_invoice_id', 'product_id','stock_id','product_name', 'quantity', 'unit_id', 'price', 'line_total'];

}
