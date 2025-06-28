<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategoriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::table('categories')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        $categories = [
            ['name' => 'Matrimonio', 'status' => 1],
            ['name' => 'Cumpleaños', 'status' => 1],
            ['name' => 'Graduación', 'status' => 1],
            ['name' => 'Corporativo', 'status'=>1],
           
        ];

        DB::table('categories')->insert($categories);

        $this->command->info('Categories table seeded with 5 event types!');
    }
}

