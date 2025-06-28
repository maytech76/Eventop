<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ImageProd extends Model
{
    use HasFactory;

   protected $table = 'images_prod';

    protected $fillable = ['prod_id', 'name'];

    public function product(){

        return $this->belongsTo(Product::class, 'prod_id');// Varias imagenes pertenecen a un producto
    }
}
