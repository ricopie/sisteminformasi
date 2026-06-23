<?php

namespace Database\Factories;

use App\Models\EducationFunding;
use App\Models\FosterChild;
use Illuminate\Database\Eloquent\Factories\Factory;

class EducationFundingFactory extends Factory
{
    protected $model = EducationFunding::class;

    public function definition(): array
    {
        return [
            'foster_child_id' => FosterChild::factory(),
            'funding_source' => fake()->randomElement(['government_scholarship', 'private_scholarship', 'family_support', 'self_funded', 'other']),
            'amount' => fake()->randomFloat(2, 1000000, 50000000),
            'currency' => 'IDR',
            'funding_start_date' => fake()->date(),
            'funding_end_date' => fake()->optional()->date(),
            'monthly_tuition_fee' => fake()->optional()->randomFloat(2, 100000, 5000000),
            'urgent_needs' => fake()->optional()->sentence(),
            'funding_status' => fake()->randomElement(['active', 'inactive', 'pending']),
        ];
    }
}
