<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
   protected $table = 'reservations';

    use HasFactory;

    protected $fillable = [

        'user_id',
        'consultant_id',
        'reservation_date',
        'start_time',
        'end_time',
        'status',
        'total_amount',
        'payment_status',
        'cancelation_reason'

    ];

    //Aplicamos un formato Global para el campo reservation_date
    public function getFormattedReservationDateAttribute(){

      return \Carbon\Carbon::parse($this->reservation_date)->format('d-m-Y');
    }

    //Definimos la relaciones con otros modelos

    public function user(){

      return $this->belongsTo(User::class);

    }

    public function consultant(){

        return $this->belongsTo(User::class, 'consultant_id');
    }

      // Relación uno a uno con 'ReservationDetail'
      public function reservationDetail(){
          // Una reserva tiene un detalle asociado
          return $this->hasOne(ReservationDetail::class);
      }



}
