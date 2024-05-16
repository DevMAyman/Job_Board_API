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
        $jobImages = [
            'https://campaignme.com/wp-content/uploads/2022/06/Website-Format-58.jpg',
            'https://upload.wikimedia.org/wikipedia/commons/thumb/a/a6/Vodafone_icon.svg/800px-Vodafone_icon.svg.png',
            'https://eghstudio.com/wp-content/uploads/2023/09/Vodafone-Intelligent-Solutions-768x283.png',
            'https://upload.wikimedia.org/wikipedia/commons/thumb/2/2b/Valeo_Logo.svg/2560px-Valeo_Logo.svg.png',
            'https://blog.logomaster.ai/hs-fs/hubfs/ibm-logo-2.jpg?width=672&height=448&name=ibm-logo-2.jpg',
            'https://www.citystars-heliopolis.com.eg/public/images/brand_logo/rkNYnmDFMx-main.jpeg?1506775959531',
            'https://s3-symbol-logo.tradingview.com/national-bank-of-kuwait--600.png',
            'https://upload.wikimedia.org/wikipedia/ar/d/de/%D8%B4%D8%B9%D8%A7%D8%B1_%D8%B3%D8%A8%D8%A7%D9%8A%D8%B1%D9%88_%D8%B3%D8%A8%D8%A7%D8%AA%D8%B3.jpg'
        ];
        $employerIds = User::where('role', 'employer')->pluck('id')->toArray();

        return [
            'user_id' => $this->faker->randomElement($employerIds),
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
            'logo' => $this->faker->randomElement($jobImages),
            'status' => $this->faker->randomElement(['pending', 'approved', 'rejected']),
        ];
    }
}
