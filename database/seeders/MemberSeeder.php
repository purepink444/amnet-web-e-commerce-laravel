<?php

namespace Database\Seeders;

use App\Models\Member;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MemberSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::where('role_id', 2)->get(); // Get all members

        foreach ($users as $user) {
            Member::firstOrCreate(['user_id' => $user->user_id], [
                'first_name' => fake()->firstName(),
                'last_name' => fake()->lastName(),
                'date_of_birth' => fake()->date('Y-m-d', '2000-01-01'),
                'address' => fake()->address(),
                'district' => 'เมือง',
                'province' => 'นครราชสีมา',
                'postal_code' => '30000',
                'profile_image' => null,
                'membership_level' => 'Bronze',
                'points' => fake()->numberBetween(0, 500),
            ]);
        }
    }
}