<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        User::create([
            'name' => 'John Davis',
            'email' => 'john@whatsapp.com',
            'password' => bcrypt('johndavis'),
        ]);
        
        \App\Models\User::factory(10)->create();

    }
}
