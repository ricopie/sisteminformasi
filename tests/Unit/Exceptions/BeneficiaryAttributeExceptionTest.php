<?php

namespace Tests\Unit\Exceptions;

use Domain\Beneficiaries\Exceptions\BeneficiaryAttributeException;
use Domain\Beneficiaries\ValueObjects\Enum\BeneficiaryType;
use DomainException;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class BeneficiaryAttributeExceptionTest extends TestCase
{
    #[Test]
    public function it_can_create_missing_attributes_exception_for_each_type(): void
    {
        $types = [BeneficiaryType::CHILD, BeneficiaryType::ELDERLY, BeneficiaryType::DISABLED, BeneficiaryType::GENERAL];
        $expectedMessages = [
            'Child beneficiaries must have child attribute data.',
            'Elderly beneficiaries must have elderly attribute data.',
            'Disability beneficiaries must have disability attribute data.',
            'General beneficiaries must have general attribute data.',
        ];

        foreach ($types as $index => $type) {
            $exception = BeneficiaryAttributeException::missingAttributes($type);
            $this->assertInstanceOf(BeneficiaryAttributeException::class, $exception);
            $this->assertInstanceOf(DomainException::class, $exception);
            $this->assertEquals($expectedMessages[$index], $exception->getMessage());
        }
    }

    #[Test]
    public function it_can_create_attributes_not_allowed_exception_for_each_type(): void
    {
        $types = [BeneficiaryType::CHILD, BeneficiaryType::ELDERLY, BeneficiaryType::DISABLED, BeneficiaryType::GENERAL];
        $expectedMessages = [
            'Custom attributes are not allowed for child types.',
            'Custom attributes are not allowed for elderly types.',
            'Custom attributes are not allowed for disability types.',
            'Custom attributes are not allowed for general types.',
        ];

        foreach ($types as $index => $type) {
            $exception = BeneficiaryAttributeException::attributesNotAllowed($type);
            $this->assertInstanceOf(BeneficiaryAttributeException::class, $exception);
            $this->assertInstanceOf(DomainException::class, $exception);
            $this->assertEquals($expectedMessages[$index], $exception->getMessage());
        }
    }

    #[Test]
    public function it_creates_missing_attributes_exception_for_child_type(): void
    {
        $exception = BeneficiaryAttributeException::missingAttributes(BeneficiaryType::CHILD);
        $this->assertEquals('Child beneficiaries must have child attribute data.', $exception->getMessage());
    }

    #[Test]
    public function it_creates_missing_attributes_exception_for_elderly_type(): void
    {
        $exception = BeneficiaryAttributeException::missingAttributes(BeneficiaryType::ELDERLY);
        $this->assertEquals('Elderly beneficiaries must have elderly attribute data.', $exception->getMessage());
    }

    #[Test]
    public function it_creates_missing_attributes_exception_for_disabled_type(): void
    {
        $exception = BeneficiaryAttributeException::missingAttributes(BeneficiaryType::DISABLED);
        $this->assertEquals('Disability beneficiaries must have disability attribute data.', $exception->getMessage());
    }

    #[Test]
    public function it_creates_missing_attributes_exception_for_general_type(): void
    {
        $exception = BeneficiaryAttributeException::missingAttributes(BeneficiaryType::GENERAL);
        $this->assertEquals('General beneficiaries must have general attribute data.', $exception->getMessage());
    }

    #[Test]
    public function it_creates_attributes_not_allowed_exception_for_child_type(): void
    {
        $exception = BeneficiaryAttributeException::attributesNotAllowed(BeneficiaryType::CHILD);
        $this->assertEquals('Custom attributes are not allowed for child types.', $exception->getMessage());
    }

    #[Test]
    public function it_creates_attributes_not_allowed_exception_for_elderly_type(): void
    {
        $exception = BeneficiaryAttributeException::attributesNotAllowed(BeneficiaryType::ELDERLY);
        $this->assertEquals('Custom attributes are not allowed for elderly types.', $exception->getMessage());
    }

    #[Test]
    public function it_creates_attributes_not_allowed_exception_for_disabled_type(): void
    {
        $exception = BeneficiaryAttributeException::attributesNotAllowed(BeneficiaryType::DISABLED);
        $this->assertEquals('Custom attributes are not allowed for disability types.', $exception->getMessage());
    }

    #[Test]
    public function it_creates_attributes_not_allowed_exception_for_general_type(): void
    {
        $exception = BeneficiaryAttributeException::attributesNotAllowed(BeneficiaryType::GENERAL);
        $this->assertEquals('Custom attributes are not allowed for general types.', $exception->getMessage());
    }
}
