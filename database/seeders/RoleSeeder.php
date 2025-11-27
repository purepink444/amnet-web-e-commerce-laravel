<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Role::firstOrCreate(['role_id' => 1], ['role_name' => 'admin']);
        Role::firstOrCreate(['role_id' => 2], ['role_name' => 'member']);
    }
}