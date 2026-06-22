<?php

namespace Database\Factories;

use App\Models\FosterChild;
use App\Models\FamilyCard;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<FosterChild>
 */
class FosterChildFactory extends Factory
{
    protected $model = FosterChild::class;

    public function definition(): array
    {
        return [
            'nik' => fake()->numerify('################'),
            'fullname' => fake()->name(),
            'nickname' => fake()->firstName(),
            'family_card_id' => FamilyCard::factory(),
            'birth_place' => fake()->city(),
            'birth_date' => fake()->date(),
            'gender' => fake()->randomElement(['M', 'F']),
        ];
    }
}
