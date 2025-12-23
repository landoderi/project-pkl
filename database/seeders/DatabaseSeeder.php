<?php
// database/seeders/DatabaseSeeder.php

namespace Database\Seeders;


use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('🌱 Sedang Mengetik...');

        // 1. Buat admin user
        User::factory()->create([
            'name' => 'Administrator',
            'email' => 'admin@example.com',
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);
        // Di dalam method run()
        User::create([
            'name' => 'Admin',
            'email' => 'admin@tokoonline.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        User::create([
            'name' => 'Customer',
            'email' => 'customer@tokoonline.com',
            'password' => Hash::make('password'),
            'role' => 'customer',   
        ]);
        $this->command->info('✅ Admin user created: admin@example.com');

        // 2. Buat beberapa customer
        User::factory(10)->create(['role' => 'customer']);
        $this->command->info('✅ 10 customer users created');



        $this->command->newLine();
        $this->command->info('🎉 Database seeding completed!');
        $this->command->info('📧 Admin login: admin@example.com / password');
    }
}
