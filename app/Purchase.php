<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    public function purchaseDetail()
    {
        return $this->hasMany('\App\PurchaseDetail', 'purchase_id', 'id');
    }

    public function supplier()
    {
        return $this->belongsTo('\App\Supplier', 'supplier_id', 'id');
    }

    protected $fillable = ['purchase_date','purchase_reference_id', 'due_date', 'image', 'supplier_id', 'supplier_name', 'sub_total', 'discount', 'tax_amount', 'grand_total', 'status', 'store_id', 'custom_purchase_id', 'note'];
}
