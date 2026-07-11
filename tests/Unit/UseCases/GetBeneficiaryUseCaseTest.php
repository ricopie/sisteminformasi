<?php

declare(strict_types=1);

namespace Tests\Unit\UseCases;

use Application\Beneficiaries\UseCases\GetBeneficiaryUseCase;
use Domain\Beneficiaries\Entities\Beneficiary;
use Domain\Beneficiaries\Repositories\BeneficiaryRepositoryInterface;
use Domain\Beneficiaries\ValueObjects\Enum\BeneficiaryType;
use Domain\Beneficiaries\ValueObjects\Enum\NationalIdentityNumber;
use Mockery;
use Mockery\MockInterface;
use PHPUnit\Framework\Attributes\Test;
use Shared\Exceptions\EntityNotFoundException;
use Shared\ValueObjects\DomainId;
use Shared\ValueObjects\Enum\Gender;
use Tests\TestCase;

final class GetBeneficiaryUseCaseTest extends TestCase
{
    private BeneficiaryRepositoryInterface&MockInterface $beneficiaries;

    private GetBeneficiaryUseCase $useCase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->beneficiaries = Mockery::mock(BeneficiaryRepositoryInterface::class);
        $this->useCase = new GetBeneficiaryUseCase($this->beneficiaries);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    #[Test]
    public function it_can_get_beneficiary_by_id(): void
    {
        $id = '01ARZ3NDEKTSV4RRFFQ69G5FAV';

        $beneficiary = Beneficiary::register(
            new NationalIdentityNumber('1234567890123456'),
            BeneficiaryType::GENERAL,
            'John Doe',
            null,
            'Jakarta',
            '1990-01-01',
            Gender::MALE,
            new DomainId($id),
        );

        $this->beneficiaries
            ->shouldReceive('findById')
            ->once()
            ->with($id)
            ->andReturn($beneficiary);

        $result = $this->useCase->handle($id);

        $this->assertSame($beneficiary, $result);
        $this->assertSame('John Doe', $result->fullName());
    }

    #[Test]
    public function it_throws_when_beneficiary_not_found(): void
    {
        $id = '01ARZ3NDEKTSV4RRFFQ69G5FAV';

        $this->beneficiaries
            ->shouldReceive('findById')
            ->once()
            ->with($id)
            ->andReturn(null);

        $this->expectException(EntityNotFoundException::class);
        $this->expectExceptionMessage(sprintf("Beneficiary with ID '%s' not found.", $id));

        $this->useCase->handle($id);
    }
}
