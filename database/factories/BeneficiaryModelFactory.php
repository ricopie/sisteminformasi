<?php

namespace Database\Factories;

use Domain\Beneficiaries\ValueObjects\Enum\BeneficiaryType;
use Domain\Beneficiaries\ValueObjects\Enum\EducationStatus;
use Domain\Beneficiaries\ValueObjects\Enum\GuardianRelationship;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Infrastructure\Beneficiaries\Models\BeneficiaryModel;
use Infrastructure\Beneficiaries\Models\FamilyCardModel;
use Infrastructure\Beneficiaries\Models\GuardianModel;
use Shared\ValueObjects\Enum\EducationLevel;
use Shared\ValueObjects\Enum\Gender;

/**
 * @extends Factory<BeneficiaryModel>
 */
class BeneficiaryModelFactory extends Factory
{
    protected $model = BeneficiaryModel::class;

    private static array $usedNiks = [];

    public function definition(): array
    {
        $type = fake()->randomElement(array_map(
            fn ($case) => $case->value,
            BeneficiaryType::cases()
        ));
        $gender = fake()->randomElement(array_map(
            fn ($case) => $case->value,
            Gender::cases()
        ));

        // Generate unique 16-digit NIK
        do {
            $nik = fake()->unique()->numerify('################');
        } while (in_array($nik, self::$usedNiks));

        self::$usedNiks[] = $nik;

        $data = [
            'nik' => $nik,
            'type' => $type,
            'full_name' => fake('id_ID')->name(),
            'nick_name' => fake('id_ID')->optional(0.3)->firstName(),
            'birth_place' => fake('id_ID')->city(),
            'birth_date' => fake()->date(max: 'now'),
            'gender' => $gender,
            'family_card_id' => FamilyCardModel::factory(),
            'is_active' => fake()->boolean(80),
        ];

        // Type-specific attributes
        if ($type === 'child') {
            $educationLevel = fake()->randomElement(array_map(
                fn ($case) => $case->value,
                EducationLevel::cases()
            ));
            $educationStatus = fake()->randomElement(array_map(
                fn ($case) => $case->value,
                EducationStatus::cases()
            ));
            $data['specific_attributes'] = [
                'education' => [
                    'level' => $educationLevel,
                    'status' => $educationStatus,
                    'schoolName' => fake()->company().' '.fake()->randomElement(['Elementary', 'Middle School', 'High School']),
                    'grade' => fake()->numberBetween(1, 12),
                    'major' => in_array($educationLevel, ['senior_high', 'diploma']) && fake()->boolean(60)
                        ? fake()->randomElement(['IPA', 'IPS', 'Bahasa', 'Teknik', 'Multimedia', null])
                        : null,
                    'nisn' => fake()->optional(0.7)->numerify('##########'),
                ],
                'educationHistory' => [],
                'hobbies' => fake()->randomElements(
                    ['Reading', 'Drawing', 'Swimming', 'Cycling', 'Gaming', 'Cooking', 'Singing', 'Dancing', 'Sports', 'Gardening'],
                    fake()->numberBetween(0, 3)
                ),
            ];
        }

        return $data;
    }

    public function configure(): static
    {
        return $this->afterCreating(function (BeneficiaryModel $beneficiary) {
            // Create 0-2 guardians
            $guardianCount = fake()->numberBetween(0, 2);
            for ($i = 0; $i < $guardianCount; $i++) {
                $guardian = new GuardianModel;
                $guardian->id = (string) Str::ulid();
                $guardian->beneficiary_id = $beneficiary->id;
                $guardian->person = [
                    'name' => fake()->name(),
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
                ];
                $guardian->relationship = fake()->randomElement(array_map(
                    fn ($case) => $case->value,
                    GuardianRelationship::cases()
                ));
                $guardian->save();
            }
        });
    }
}
