<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([

            'rol_id'=> 1, //Administrador
            'name' => 'Marco Antonio',
            'last_name'=> 'Yanez',
            'email' => 'marcoyanez@gmail.com',
            'profile_photo_path' => null,
            'password' => Hash::make(12345678),
            'phone'=> '+56987456321'

        ]);

        User::create([

            'rol_id'=> 1, //Administrador
            'name' => 'Mario Argenis',
            'last_name'=> 'Yanez',
            'email' => 'marioa@gmail.com',
            'profile_photo_path' => null,
            'password' => Hash::make(12345678),
            'phone'=> '+56987456322'

        ]);

        //El resto de los campos se agregan segun la configuracion del factories->UserFactory

        //producir datos para 3 usuarios con rol Consultor
        User::factory()->count(3)->create([

            'rol_id'=> 2, // Consultores

        ]);

         //producir datos para 10 usuarios con rol Usuarios generales
        User::factory()->count(10)->create([

            'rol_id'=> 3, // Usuarios Generales
        ]);
    }
}
