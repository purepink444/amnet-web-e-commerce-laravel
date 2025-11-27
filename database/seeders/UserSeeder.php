<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::firstOrCreate(['username' => 'admin'], [
            'username' => 'admin',
            'email' => 'admin@amnet.co.th',
            'password' => bcrypt('password'),
            'phone' => '0812345678',
            'is_active' => true,
            'role_id' => 1,
        ]);

        User::firstOrCreate(['username' => 'testuser'], [
            'username' => 'testuser',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
            'is_active' => true,
            'role_id' => 2,
        ]);

        User::firstOrCreate(['username' => 'john_doe'], [
            'username' => 'john_doe',
            'email' => 'john@example.com',
            'password' => bcrypt('password'),
            'phone' => '0812345678',
            'is_active' => true,
            'role_id' => 2,
        ]);

        User::firstOrCreate(['username' => 'jane_smith'], [
            'username' => 'jane_smith',
            'email' => 'jane@example.com',
            'password' => bcrypt('password'),
            'phone' => '0898765432',
            'is_active' => true,
            'role_id' => 2,
        ]);
    }
}