<?php

namespace Database\Seeders;

use App\Models\Event;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;


class GuestSeeder extends Seeder
{
    
    private $usedPhones = [];

    public function run(): void{

        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::table('guests')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        // Obtener el primer evento de prueba
        $event = Event::first();
        
        if (!$event) {
            $this->command->error('No events found! Please run EventsTableSeeder first.');
            return;
        }

        // Crear directorio para fotos si no existe
        if (!Storage::exists('public/guests')) {
            Storage::makeDirectory('public/guests');
        }

        $guestStatuses = ['checkIn', 'active', 'CheckOut', 'retire'];
        $photoPaths = [
            null, // Para algunos invitados sin foto
            'guests/avatar1.jpg',
            'guests/avatar2.jpg',
            'guests/avatar3.jpg',
            'guests/avatar4.jpg',
            'guests/avatar5.jpg',
            'guests/avatar1.jpg',
            'guests/avatar2.jpg',
            'guests/avatar3.jpg',
            'guests/avatar4.jpg',
            'guests/avatar5.jpg',
            'guests/avatar1.jpg',
            'guests/avatar2.jpg',
            'guests/avatar3.jpg',
            'guests/avatar4.jpg',
            'guests/avatar5.jpg',
            'guests/avatar1.jpg',
            'guests/avatar2.jpg',
            'guests/avatar3.jpg',
            'guests/avatar4.jpg',
            'guests/avatar5.jpg',
        ];

        // Copiar fotos de ejemplo a storage
        $this->copySamplePhotos();

        for ($i = 0; $i < 20; $i++) {
            $firstName = fake()->firstName();
            $lastName = fake()->lastName();
            $codeIn = 'EVT-' . Str::random(8) . '-' . time();
            $photoPath = $photoPaths[array_rand($photoPaths)];
            $phone = $this->generateUniquePhone();

            DB::table('guests')->insert([

                'event_id' => 1,
                'photo' => $photoPath,
                'name' => $firstName,
                'last_name' => $lastName,
                'phone' => $phone,
                'email' => Str::slug($firstName . '.' . $lastName) . '@example.com',
                'code_in' => $codeIn,
                'qr_code_path' => 'qr_codes/' . $codeIn . '.svg',
                'is_attended' => fake()->boolean(30),
                'attendance_time' => fake()->boolean(30) ? now() : null,
                'status' => $guestStatuses[array_rand($guestStatuses)],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $this->command->info('Guests table seeded with 20 random guests including photos!');
    }

    protected function generateUniquePhone(){
        do {
            // Generar número en formato: +51 9XX XXX XXX (ejemplo para Perú)
            $phone = '+58 9' . rand(10, 99) . ' ' . rand(100, 999) . ' ' . rand(100, 999);
            
            // Alternativa más simple si prefieres:
            // $phone = '9' . rand(10000000, 99999999);
            
        } while (in_array($phone, $this->usedPhones));

        $this->usedPhones[] = $phone;
        return $phone;
    }

    protected function copySamplePhotos()
    {
        $photos = [
            'avatar1.png',
            'avatar2.png',
            'avatar3.png'
        ];

        foreach ($photos as $photo) {
            $source = database_path('seeders/sample_photos/' . $photo);
            $dest = storage_path('app/public/guests/' . $photo);
            
            if (file_exists($source) && !file_exists($dest)) {
                copy($source, $dest);
            }
        }
    }
       
}




