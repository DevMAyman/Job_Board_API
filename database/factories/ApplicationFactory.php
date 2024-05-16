<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Application;
use App\Models\User;
use App\Models\JobListing;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Application>
 */
class ApplicationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $resume_sample = [
            'https://www.uvic.ca/career-services/_assets/docs/resume-computer-engineering.pdf',
            'https://www.dayjob.com/downloads/CV_examples/Junior_software_developer.pdf',
            'https://deepmehta.co.in/resume.pdf',
            'https://tolustar.com/resume.pdf',
            'https://careerdocs.charlotte.edu/resumes/AMD/Graphic%20Designer%20Resume%20Example.pdf',
            'https://www.dayjob.com/downloads/CV_examples/graphic_designer_cv_example.pdf',
            'https://www.snc.edu/careers/docs/GraphicDesignResume.pdf'
        ];
        $candidate_ids = User::where('role', 'candidate')->pluck('id')->toArray();
        $jobs_ids = JobListing::where('status','approved')->pluck('id')->toArray();
        return [
            //
            'email' => $this->faker->unique()->safeEmail,
            'phoneNumber' => $this->faker->phoneNumber,
            'resume' => $this->faker->randomElement($resume_sample),
            'status' => 'pending',
            'user_id' => $this->faker->randomElement($candidate_ids),
            'job_listings_id' => $this->faker->randomElement($jobs_ids), 
        ];
    }
}
