<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Partner extends Model
{
    use HasFactory;

    protected $fillable = [

        'name',
        'guest_id',
        'checkin_time',
        'status'
    ];

    protected $table = 'partners';

    function guest(){

        return $this->belongsTo(Guest::class);
        
    }


}
