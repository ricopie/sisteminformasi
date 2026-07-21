<?php

declare(strict_types=1);

namespace Tests\Unit\Contexts\Beneficiary\Application\Command;

use Copie\Contexts\Beneficiary\Application\Command\UpdateBeneficiaryStatusCommand;
use Copie\Shared\Domain\Validator\ValidationException;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class UpdateBeneficiaryStatusCommandTest extends TestCase
{
    #[Test]
    public function test_from_array_creates_command_with_id_and_is_active(): void
    {
        $data = ['id' => '...', 'isActive' => false];

        $updateBeneficiaryStatusCommand = UpdateBeneficiaryStatusCommand::fromArray($data);

        $this->assertSame($data['id'], $updateBeneficiaryStatusCommand->id);
        $this->assertSame($data['isActive'], $updateBeneficiaryStatusCommand->isActive);
    }

    #[Test]
    public function test_from_array_creates_command_with_is_active_true(): void
    {
        $data = ['id' => '...', 'isActive' => true];

        $updateBeneficiaryStatusCommand = UpdateBeneficiaryStatusCommand::fromArray($data);

        $this->assertTrue($updateBeneficiaryStatusCommand->isActive);
    }

    #[Test]
    public function test_from_array_throws_validation_exception_when_id_is_empty(): void
    {
        $this->expectException(ValidationException::class);

        $data = ['id' => '', 'isActive' => true];

        UpdateBeneficiaryStatusCommand::fromArray($data);
    }

    #[Test]
    public function test_to_array_returns_all_fields(): void
    {
        $updateBeneficiaryStatusCommand = new UpdateBeneficiaryStatusCommand(id: 'x', isActive: true);

        $this->assertSame(['id' => 'x', 'isActive' => true], $updateBeneficiaryStatusCommand->toArray());
    }
}
