<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = ['category_id', 'name', 'description', 'price', 'status'];

    public function images(){

        return $this->hasMany(ImageProd::class, 'prod_id');

    }
    

    public function category(){

        return $this->belongsTo(Category::class);
    }

    public function warehouses(){ /* to product belongs to many warehouses */

        return $this->belongsToMany(Warehouse::class)
                    ->withPivot('stock')
                    ->withTimestamps();
   }

   public function product_warehouse(){

     return $this->hansMany(Product_warehouse::class, 'prod_id');

   }
}
