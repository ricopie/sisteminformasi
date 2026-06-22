<?php

namespace Database\Factories;

use App\Models\FamilyCard;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<FamilyCard>
 */
class FamilyCardFactory extends Factory
{
    protected $model = FamilyCard::class;

    public function definition(): array
    {
        return [
            'family_card_number' => fake()->numerify('################'),
            'head_of_family_name' => fake()->name(),
            'address' => fake()->address(),
            'rt' => fake()->numerify('###'),
            'rw' => fake()->numerify('###'),
            'village' => fake()->city(),
            'sub_district' => fake()->city(),
            'city' => fake()->city(),
            'province' => fake()->state(),
            'postal_code' => fake()->numerify('#####'),
        ];
    }
}
