<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //generating random 20 user candidates and employers 
        User::factory()->count(10)->state(['role' => 'employer'])->create();
        User::factory()->count(10)->state(['role' => 'candidate'])->create();

    }
}
