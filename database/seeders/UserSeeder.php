<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::table('users')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        // Obtener IDs de roles
        $adminRoleId = DB::table('roles')->where('name', 'admin')->first()->id;
        $organizerRoleId = DB::table('roles')->where('name', 'organizer')->first()->id;
        $guestRoleId = DB::table('roles')->where('name', 'guest')->first()->id;

        $users = [
            // Administrador
            [
                'rol_id' => $adminRoleId,
                'name' => 'Marco Antonio',
                'last_name' => 'Yanez',
                'email' => 'staroffic@gmail.com',
                'phone' => '1234567890',
                'email_verified_at' => now(),
                'password' => Hash::make('12345678'),
                'remember_token' => Str::random(10),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            
            // Organizador
            [
                'rol_id' => $organizerRoleId,
                'name' => 'Organizador',
                'last_name' => 'Eventos',
                'email' => 'organizer@guestcontrol.com',
                'phone' => '9876543210',
                'email_verified_at' => now(),
                'password' => Hash::make('Organizer123'),
                'remember_token' => Str::random(10),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            
            // Invitado
            [
                'rol_id' => $guestRoleId,
                'name' => 'Invitado',
                'last_name' => 'Demo',
                'email' => 'guest@guestcontrol.com',
                'phone' => '5551234567',
                'email_verified_at' => now(),
                'password' => Hash::make('Guest1234'),
                'remember_token' => Str::random(10),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('users')->insert($users);

        $this->command->info('Users table seeded with 3 users (admin, organizer, guest)!');
    }
}

