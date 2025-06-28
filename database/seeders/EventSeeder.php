<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;


class EventSeeder extends Seeder{

   
    public function run(): void{
        
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::table('events')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        $organizerId = DB::table('users')
                       ->where('email', 'organizer@guestcontrol.com')
                       ->first()->id;

        $categories = DB::table('categories')->get();

        $events = [
            [
                'user_id' => $organizerId,
                'category_id' => $categories->where('name', 'Matrimonio')->first()->id,
                'price' => 500,
                'title' => 'Boda de Ana y Carlos',
                'description' => 'Ceremonia y recepción de boda',
                'address' => 'Hacienda Las Flores, Av. Principal 123',
                'event_date' => now()->addDays(15),
                'start_time' => '19:00:00',
                'end_time' => '04:00:00',
                'limit_guest' => 150,
                'rooms' => 'Jardín Principal',
                'status' => 'activo',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ];

        DB::table('events')->insert($events);

        $this->command->info('Events table seeded with sample events!');
    }
}
