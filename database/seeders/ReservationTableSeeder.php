<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Reservation;

class ReservationTableSeeder extends Seeder
{
    /**
     * Se define cuantos registros se van a fabricar a un modelo especifico
     */
    public function run(): void
    {
        Reservation::factory()->count(20)->create();
    }
}
