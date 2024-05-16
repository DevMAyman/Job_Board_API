<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //generating seeeder to create random 20 user candidates and employers
        User::factory()->count(20)->state(['role' => 'employer', 'image' => 'my image'])->create();
        User::factory()->count(10)->state(['role' => 'candidate', 'image' => 'my image'])->create();
    }
}
