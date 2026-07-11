<?php

namespace Tests\Unit\DTOs;

use Application\Beneficiaries\DTOs\GuardianData;
use Domain\Beneficiaries\ValueObjects\Enum\GuardianRelationship;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class GuardianDataTest extends TestCase
{
    #[Test]
    public function it_can_create_via_constructor_with_person_and_relationship(): void
    {
        $person = [
            'name' => 'John Doe',
            'occupation' => 'Engineer',
        ];

        $guardian = new GuardianData(
            person: $person,
            relationship: GuardianRelationship::FATHER,
        );

        $this->assertSame($person['name'], $guardian->person['name']);
        $this->assertSame($person['occupation'], $guardian->person['occupation']);
        $this->assertSame(GuardianRelationship::FATHER->value, $guardian->relationship->value);
    }

    #[Test]
    public function it_can_create_via_static_from(): void
    {
        $data = [
            'person' => [
                'name' => 'Jane Doe',
                'occupation' => 'Teacher',
                'education' => 'Bachelor Degree',
                'address' => [
                    'street' => '456 Oak St',
                    'rt' => '03',
                    'rw' => '04',
                    'village' => 'East',
                    'district' => 'East District',
                    'city' => 'East City',
                    'province' => 'East Province',
                    'postal_code' => '54321',
                ],
                'contact' => [
                    'phone' => '123456789',
                    'email' => 'jane@example.com',
                ],
            ],
            'relationship' => 'father',
        ];

        $guardian = GuardianData::from($data);

        $this->assertSame($data['person']['name'], $guardian->person['name']);
        $this->assertSame($data['person']['occupation'], $guardian->person['occupation']);
        $this->assertSame($data['person']['education'], $guardian->person['education']);
        $this->assertSame($data['person']['address'], $guardian->person['address']);
        $this->assertSame($data['person']['contact'], $guardian->person['contact']);
        $this->assertSame(GuardianRelationship::FATHER->value, $guardian->relationship->value);
    }

    #[Test]
    public function it_to_array_returns_correct_structure(): void
    {
        $person = [
            'name' => 'John Doe',
            'occupation' => 'Engineer',
        ];

        $guardian = new GuardianData(
            person: $person,
            relationship: GuardianRelationship::FATHER,
        );

        $arrayData = $guardian->toArray();

        $this->assertSame($person['name'], $arrayData['person']['name']);
        $this->assertSame($person['occupation'], $arrayData['person']['occupation']);
        $this->assertSame(GuardianRelationship::FATHER->value, $arrayData['relationship']);
    }

    #[Test]
    public function it_can_create_with_all_optional_fields(): void
    {
        $person = [
            'name' => 'Bob Smith',
            'occupation' => 'Doctor',
            'education' => 'Medical School',
            'address' => [
                'street' => '789 Pine St',
                'rt' => '05',
                'rw' => '06',
                'village' => 'West',
                'district' => 'West District',
                'city' => 'West City',
                'province' => 'West Province',
                'postal_code' => '98765',
            ],
            'contact' => [
                'phone' => '987654321',
                'email' => 'bob@example.com',
            ],
        ];

        $guardian = new GuardianData(
            person: $person,
            relationship: GuardianRelationship::MOTHER,
        );

        $this->assertSame($person['name'], $guardian->person['name']);
        $this->assertSame($person['occupation'], $guardian->person['occupation']);
        $this->assertSame($person['education'], $guardian->person['education']);
        $this->assertSame($person['address'], $guardian->person['address']);
        $this->assertSame($person['contact'], $guardian->person['contact']);
        $this->assertSame(GuardianRelationship::MOTHER->value, $guardian->relationship->value);
    }
}
