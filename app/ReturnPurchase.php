<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ReturnPurchase extends Model
{
    public function returnPurchaseDetail()
    {
        return $this->hasMany('\App\ReturnPurchaseDetail', 'return_purchase_id', 'id');
    }

    public function supplier()
    {
        return $this->belongsTo('\App\Supplier', 'supplier_id', 'id');
    }

    protected $fillable = ['return_purchase_date','return_purchase_reference_id', 'due_date', 'image', 'supplier_id', 'supplier_name', 'sub_total', 'discount', 'tax_amount', 'grand_total', 'status', 'store_id', 'custom_return_purchase_id', 'note'];

}
