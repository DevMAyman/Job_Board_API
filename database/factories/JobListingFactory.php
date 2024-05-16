<?php

namespace Database\Factories;

use App\Models\JobListing;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;
class JobListingFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = JobListing::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'title' => $this->faker->jobTitle,
            'description' => $this->faker->paragraph,
            'responsibilities' => $this->faker->paragraph,
            'skills' => $this->faker->words(5, true),
            'qualifications' => $this->faker->words(5, true),
            'salary_range' => $this->faker->numberBetween(30000, 100000),
            'benefits' => $this->faker->paragraph,
            'location' => $this->faker->city,
            'work_type' => $this->faker->randomElement(['on-site', 'remote', 'hybrid']),
            'application_deadline' => $this->faker->dateTimeBetween('tomorrow', '+1 month'),
            'logo' => 'https://t3.ftcdn.net/jpg/05/56/23/24/360_F_556232429_rEzqcNG3dwJlhlxlShtfPYOJ4BcZBSlW.jpg',
            'status' => $this->faker->randomElement(['pending', 'approved', 'rejected']),
        ];
    }
}
