<?php

namespace Database\Factories;

use Domain\Beneficiaries\ValueObjects\Enum\GuardianRelationship;
use Illuminate\Database\Eloquent\Factories\Factory;
use Infrastructure\Beneficiaries\Models\GuardianModel;
use Shared\ValueObjects\Enum\EducationLevel;

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
                'education' => fake()->randomElement([
                    ...array_map(fn ($case) => $case->value, EducationLevel::cases()),
                    null,
                ]),
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
                    'phone' => fake()->randomElement([
                        '08'.fake()->numerify('##########'),    // 08 + 10 digit = 12 total
                        '+628'.fake()->numerify('########'),    // +628 + 8 digit = 12 total
                        '0812'.fake()->numerify('########'),    // 0812 + 8 digit = 12 total
                        '0878'.fake()->numerify('########'),    // 0878 + 8 digit = 12 total
                    ]),
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
