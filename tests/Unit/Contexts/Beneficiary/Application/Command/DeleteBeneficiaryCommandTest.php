<?php

declare(strict_types=1);

namespace Tests\Unit\Contexts\Beneficiary\Application\Command;

use Copie\Contexts\Beneficiary\Application\Command\DeleteBeneficiaryCommand;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class DeleteBeneficiaryCommandTest extends TestCase
{
    #[Test]
    public function test_from_array_creates_command_with_id(): void
    {
        $data = ['id' => '01JARZ3NDEKTSV4RRFFXJJNW43'];

        $deleteBeneficiaryCommand = DeleteBeneficiaryCommand::fromArray($data);

        $this->assertSame($data['id'], $deleteBeneficiaryCommand->id);
    }

    #[Test]
    public function test_to_array_returns_id(): void
    {
        $deleteBeneficiaryCommand = new DeleteBeneficiaryCommand(id: 'test-id');

        $this->assertSame(['id' => 'test-id'], $deleteBeneficiaryCommand->toArray());
    }

    #[Test]
    public function test_round_trip(): void
    {
        $original = ['id' => 'abc123'];
        $deleteBeneficiaryCommand = DeleteBeneficiaryCommand::fromArray($original);
        $result = $deleteBeneficiaryCommand->toArray();

        $this->assertSame($original, $result);
    }
}
