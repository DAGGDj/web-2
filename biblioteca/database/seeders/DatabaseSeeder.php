<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Faker\Factory as FakerFactory;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        User::create([
            'name' => 'Administrador',
            'email' => 'admin@biblioteca.com',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
        ]);

        User::create([
            'name' => 'Bibliotecário',
            'email' => 'bibliotecario@biblioteca.com',
            'password' => Hash::make('bibliotecario123'),
            'role' => 'bibliotecario',
        ]);

        User::create([
            'name' => 'Cliente',
            'email' => 'cliente@biblioteca.com',
            'password' => Hash::make('cliente123'),
            'role' => 'cliente',
        ]);
        
        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        FakerFactory::create()->unique(true);
        
        $this->call([
            CategorySeeder::class,
            AuthorPublisherBookSeeder::class,
            UserBorrowingSeeder::class,
        ]);
    }
}