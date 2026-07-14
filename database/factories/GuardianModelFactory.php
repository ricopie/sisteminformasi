<?php

namespace Database\Factories;

use Domain\Beneficiaries\ValueObjects\Enum\GuardianRelationship;
use Illuminate\Database\Eloquent\Factories\Factory;
use Infrastructure\Beneficiaries\Models\GuardianModel;

/**
 * @extends Factory<GuardianModel>
 */
class GuardianModelFactory extends Factory
{
    protected $model = GuardianModel::class;

    public function definition(): array
    {
        return [
            'person' => [
                'name' => fake('id_ID')->name(),
                'occupation' => fake()->optional(0.7)->jobTitle(),
                'education' => fake()->randomElement(['elementary', 'junior_high', 'senior_high', 'diploma', 'bachelor', 'master', null]),
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
                'contact' => [
                    'phone' => fake()->phoneNumber(),
                    'email' => fake()->email(),
                ],
            ],
            'relationship' => fake()->randomElement(array_map(
                fn ($case) => $case->value,
                GuardianRelationship::cases()
            )),
        ];
    }
}
