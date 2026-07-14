<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Infrastructure\Beneficiaries\Models\FamilyCardModel;

/**
 * @extends Factory<FamilyCardModel>
 */
class FamilyCardModelFactory extends Factory
{
    protected $model = FamilyCardModel::class;

    public function definition(): array
    {
        return [
            'number' => fake()->numerify('################'),
            'head_of_family_name' => fake('id_ID')->name(),
            'address' => [
                'street' => fake()->streetAddress(),
                'rt' => fake()->numerify('###'),
                'rw' => fake()->numerify('###'),
                'village' => fake()->city(),
                'district' => fake()->city(),
                'city' => fake()->city(),
                'province' => fake()->state(),
                'postal_code' => fake()->numerify('#####'),
            ],
        ];
    }
}
