<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class Event2 extends Model
{
    use HasFactory;
    
    protected $table = 'events';

    protected $fillable = [

        'user_id',
        'category_id', 
        'price', 
        'title', 
        'description', 
        'address', 
        'event_date', 
        'start_time', 
        'end_time', 
        'limit_guest',
        'limit_partners',
        'certificate_template',
        'has_certificate',
        'type',
        'status',
        'rooms', 
        
    ];

    public function user(){

        return $this->belongsTo(User::class);
    }
    
    public function category(){

        return $this->belongsTo(Category::class);
    }
    

    public function guests(){
        
        return $this->hasMany(Guest::class);
    }

    

    public function getStatusClassAttribute(){

        $classes = [
            'reservado'  => 'btn-reserva-light',
            'activo'     => 'btn-activo-light',
            'culminado'  => 'btn-culminado-light',
            'cancelado'  => 'btn-cancelado-light',
        ];

        $key = strtolower(trim($this->status));

        Log ::info("Estado recibido: '{$this->status}' => key normalizado: '{$key}'");

        return $classes[$key] ?? 'btn-secondary';
    }
}
