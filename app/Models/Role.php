<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{

    protected $fillable = [

       'name'

    ];

    use HasFactory;

    public function users(){

      return $this->hasMany(User::class, 'rol_id');

    }
}
