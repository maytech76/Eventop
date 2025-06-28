<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product_warehouse extends Model
{
    use HasFactory;

    protected $table = 'product_warehouse';

    protected $fillable = [

        'product_id',
        'warehouse_id',
        'stock'
    ];

    public function product(){
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }

    public function warehouse(){
        return $this->belongsTo(Warehouse::class, 'warehouse_id', 'id');
    }
}
