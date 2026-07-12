<?php

namespace Tests\Unit\DTOs;

use Application\Beneficiaries\DTOs\FamilyCardData;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class FamilyCardDataTest extends TestCase
{
    #[Test]
    public function it_can_create_via_constructor_with_all_fields(): void
    {
        $data = [
            'number' => '1234567890',
            'head_of_family_name' => 'John Doe',
            'address' => [
                'street' => '123 Main St',
                'rt' => '01',
                'rw' => '02',
                'village' => 'Central',
                'district' => 'District',
                'city' => 'City',
                'province' => 'Province',
                'postal_code' => '12345',
            ],
        ];

        $familyCard = new FamilyCardData(
            number: $data['number'],
            head_of_family_name: $data['head_of_family_name'],
            address: $data['address'],
        );

        $this->assertSame($data['number'], $familyCard->number);
        $this->assertSame($data['head_of_family_name'], $familyCard->head_of_family_name);
        $this->assertSame($data['address'], $familyCard->address);
    }

    #[Test]
    public function it_can_create_via_static_from_with_same_array(): void
    {
        $data = [
            'number' => '1234567890',
            'head_of_family_name' => 'John Doe',
            'address' => [
                'street' => '123 Main St',
                'rt' => '01',
                'rw' => '02',
                'village' => 'Central',
                'district' => 'District',
                'city' => 'City',
                'province' => 'Province',
                'postal_code' => '12345',
            ],
        ];

        $familyCard = FamilyCardData::from($data);

        $this->assertSame($data['number'], $familyCard->number);
        $this->assertSame($data['head_of_family_name'], $familyCard->head_of_family_name);
        $this->assertSame($data['address'], $familyCard->address);
    }

    #[Test]
    public function it_to_array_returns_correct_structure(): void
    {
        $data = [
            'number' => '1234567890',
            'head_of_family_name' => 'John Doe',
            'address' => [
                'street' => '123 Main St',
                'rt' => '01',
                'rw' => '02',
                'village' => 'Central',
                'district' => 'District',
                'city' => 'City',
                'province' => 'Province',
                'postal_code' => '12345',
            ],
        ];

        $familyCard = new FamilyCardData(
            number: $data['number'],
            head_of_family_name: $data['head_of_family_name'],
            address: $data['address'],
        );

        $arrayData = $familyCard->toArray();

        $this->assertSame($data['number'], $arrayData['number']);
        $this->assertSame($data['head_of_family_name'], $arrayData['head_of_family_name']);
        $this->assertSame($data['address'], $arrayData['address']);
    }
}
