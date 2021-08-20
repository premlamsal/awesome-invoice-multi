<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    public function category()
    {
        return $this->belongsTo('\App\ProductCategory', 'product_cat_id', 'id');
    }


    public function productDetail()
    {

        return $this->hasMany('\App\productDetail', 'product_id', 'id');
    }

    public function stock()
    {

        return $this->hasMany('\App\Stock', 'product_id', 'id');
    }
    public function unit()
    {
        return $this->belongsTo('\App\Unit', 'unit_id', 'id');
    }

}
