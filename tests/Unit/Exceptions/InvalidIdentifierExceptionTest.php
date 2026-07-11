<?php

namespace Tests\Unit\Exceptions;

use Domain\Beneficiaries\ValueObjects\Enum\EducationStatus;
use Domain\Beneficiaries\ValueObjects\Enum\NationalIdentityNumber;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\Test;
use Shared\Exceptions\InvalidIdentifierException;
use Tests\TestCase;

class InvalidIdentifierExceptionTest extends TestCase
{
    #[Test]
    public function it_can_create_exception_for_identifier(): void
    {
        $exception = InvalidIdentifierException::for('invalid-nik', NationalIdentityNumber::class);
        $this->assertInstanceOf(InvalidIdentifierException::class, $exception);
        $this->assertInstanceOf(InvalidArgumentException::class, $exception);
        $this->assertStringContainsString('Invalid NationalIdentityNumber: "invalid-nik".', $exception->getMessage());
    }

    #[Test]
    public function it_contains_correct_identifier_and_type_in_message(): void
    {
        $exception = InvalidIdentifierException::for('test-value', EducationStatus::class);
        $this->assertStringContainsString('Invalid EducationStatus: "test-value".', $exception->getMessage());
    }

    #[Test]
    public function it_handles_empty_identifier(): void
    {
        $exception = InvalidIdentifierException::for('', 'Domain\\Beneficiaries\\ValueObjects\\Enum\\Gender');
        $this->assertInstanceOf(InvalidIdentifierException::class, $exception);
        $this->assertStringContainsString('Invalid Gender: "".', $exception->getMessage());
    }
}
