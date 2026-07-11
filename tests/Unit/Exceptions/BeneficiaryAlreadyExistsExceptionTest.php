<?php

namespace Tests\Unit\Exceptions;

use Domain\Beneficiaries\Exceptions\BeneficiaryAlreadyExistsException;
use PHPUnit\Framework\Attributes\Test;
use RuntimeException;
use Tests\TestCase;

class BeneficiaryAlreadyExistsExceptionTest extends TestCase
{
    #[Test]
    public function it_can_create_exception_for_nik(): void
    {
        $exception = BeneficiaryAlreadyExistsException::forNik();
        $this->assertInstanceOf(BeneficiaryAlreadyExistsException::class, $exception);
        $this->assertInstanceOf(RuntimeException::class, $exception);
        $this->assertEquals('A beneficiary with this NIK is already registered.', $exception->getMessage());
    }

    #[Test]
    public function it_has_standard_message(): void
    {
        $exception = BeneficiaryAlreadyExistsException::forNik();
        $this->assertStringContainsString('already registered', $exception->getMessage());
    }
}
