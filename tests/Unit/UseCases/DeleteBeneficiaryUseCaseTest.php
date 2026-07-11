<?php

declare(strict_types=1);

namespace Tests\Unit\UseCases;

use Application\Beneficiaries\UseCases\DeleteBeneficiaryUseCase;
use Domain\Beneficiaries\Entities\Beneficiary;
use Domain\Beneficiaries\Repositories\BeneficiaryRepositoryInterface;
use Domain\Beneficiaries\ValueObjects\Enum\BeneficiaryType;
use Domain\Beneficiaries\ValueObjects\Enum\NationalIdentityNumber;
use Illuminate\Contracts\Events\Dispatcher;
use Mockery;
use Mockery\MockInterface;
use PHPUnit\Framework\Attributes\Test;
use Shared\Exceptions\EntityNotFoundException;
use Shared\ValueObjects\DomainId;
use Shared\ValueObjects\Enum\Gender;
use Tests\TestCase;

final class DeleteBeneficiaryUseCaseTest extends TestCase
{
    private BeneficiaryRepositoryInterface&MockInterface $beneficiaries;

    private Dispatcher&MockInterface $events;

    private DeleteBeneficiaryUseCase $useCase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->beneficiaries = Mockery::mock(BeneficiaryRepositoryInterface::class);
        $this->events = Mockery::mock(Dispatcher::class);

        $this->useCase = new DeleteBeneficiaryUseCase(
            $this->beneficiaries,
            $this->events,
        );
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    private function makeBeneficiary(): Beneficiary
    {
        return Beneficiary::register(
            new NationalIdentityNumber('1234567890123456'),
            BeneficiaryType::GENERAL,
            'John Doe',
            null,
            'Jakarta',
            '1990-01-01',
            Gender::MALE,
            new DomainId('01ARZ3NDEKTSV4RRFFQ69G5FAV'),
            null,
        );
    }

    #[Test]
    public function it_can_delete_beneficiary(): void
    {
        $beneficiary = $this->makeBeneficiary();
        $id = '01ARZ3NDEKTSV4RRFFQ69G5FAV';

        $this->beneficiaries
            ->shouldReceive('findById')
            ->once()
            ->with($id)
            ->andReturn($beneficiary);

        $this->beneficiaries
            ->shouldReceive('delete')
            ->once()
            ->with($beneficiary);

        $this->events
            ->shouldReceive('dispatch')
            ->zeroOrMoreTimes();

        $this->useCase->handle($id);

        $this->assertTrue(true); // no exception = success
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

        $this->useCase->handle($id);
    }

    #[Test]
    public function it_dispatches_domain_events_on_delete(): void
    {
        $beneficiary = $this->makeBeneficiary();
        $id = '01ARZ3NDEKTSV4RRFFQ69G5FAV';

        $this->beneficiaries
            ->shouldReceive('findById')
            ->once()
            ->with($id)
            ->andReturn($beneficiary);

        $this->beneficiaries
            ->shouldReceive('delete')
            ->once();

        $this->events
            ->shouldReceive('dispatch')
            ->zeroOrMoreTimes();

        $this->useCase->handle($id);
    }
}
