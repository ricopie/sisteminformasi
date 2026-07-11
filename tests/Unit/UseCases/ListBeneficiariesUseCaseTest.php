<?php

declare(strict_types=1);

namespace Tests\Unit\UseCases;

use Application\Beneficiaries\Queries\BeneficiaryQueryInterface;
use Application\Beneficiaries\UseCases\ListBeneficiariesUseCase;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Mockery;
use Mockery\MockInterface;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class ListBeneficiariesUseCaseTest extends TestCase
{
    private BeneficiaryQueryInterface&MockInterface $query;

    private ListBeneficiariesUseCase $useCase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->query = Mockery::mock(BeneficiaryQueryInterface::class);
        $this->useCase = new ListBeneficiariesUseCase($this->query);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    #[Test]
    public function it_can_list_beneficiaries_with_default_pagination(): void
    {
        $paginator = Mockery::mock(LengthAwarePaginator::class);
        $paginator->shouldReceive('items')->andReturn(new Collection);
        $paginator->shouldReceive('total')->andReturn(0);

        $this->query
            ->shouldReceive('findAllPaginated')
            ->once()
            ->with([], 15)
            ->andReturn($paginator);

        $result = $this->useCase->handle();

        $this->assertInstanceOf(LengthAwarePaginator::class, $result);
    }

    #[Test]
    public function it_can_list_beneficiaries_with_filters(): void
    {
        $filters = ['type' => 'CHILD', 'search' => 'John'];

        $paginator = Mockery::mock(LengthAwarePaginator::class);
        $paginator->shouldReceive('items')->andReturn(new Collection);
        $paginator->shouldReceive('total')->andReturn(0);

        $this->query
            ->shouldReceive('findAllPaginated')
            ->once()
            ->with($filters, 15)
            ->andReturn($paginator);

        $result = $this->useCase->handle($filters);

        $this->assertInstanceOf(LengthAwarePaginator::class, $result);
    }

    #[Test]
    public function it_can_list_beneficiaries_with_custom_per_page(): void
    {
        $perPage = 25;

        $paginator = Mockery::mock(LengthAwarePaginator::class);
        $paginator->shouldReceive('items')->andReturn(new Collection);
        $paginator->shouldReceive('total')->andReturn(0);

        $this->query
            ->shouldReceive('findAllPaginated')
            ->once()
            ->with([], $perPage)
            ->andReturn($paginator);

        $result = $this->useCase->handle([], $perPage);

        $this->assertInstanceOf(LengthAwarePaginator::class, $result);
    }
}
