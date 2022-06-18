<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ReturnInvoice extends Model
{
    public function returnInvoiceDetail()
    {
        return $this->hasMany('\App\ReturnInvoiceDetail', 'return_invoice_id', 'id');
    }

    public function customer()
    {
        return $this->belongsTo('\App\Customer', 'customer_id', 'id');
    }

    protected $fillable = ['return_invoice_date', 'due_date', 'customer_id', 'customer_name', 'sub_total', 'discount', 'tax_amount', 'grand_total', 'status', 'store_id', 'custom_return_invoice_id', 'note'];

}
