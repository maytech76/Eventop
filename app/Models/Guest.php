<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Guest extends Model
{
        use HasFactory;
  

        protected $dates = ['deleted_at'];

        protected $fillable = [
            
            'event_id', 
            'photo', 
            'name', 
            'last_name', 
            'phone', 
            'email',
            'code_in', 
            'checkin_time',
            'checkout_time',
            'qr_code_path', 
            'is_attended', 
            'attendance_time', 
            'status',
            'email_sent'
        ];
        
        public function event()
        {
            return $this->belongsTo(Event::class);
        }
    
   }

