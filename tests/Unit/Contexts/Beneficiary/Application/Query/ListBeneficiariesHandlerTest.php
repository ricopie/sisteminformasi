<?php

declare(strict_types=1);

namespace Tests\Unit\Contexts\Beneficiary\Application\Query;

use Copie\Contexts\Beneficiary\Application\BeneficiaryQueryInterface;
use Copie\Contexts\Beneficiary\Application\Query\ListBeneficiariesHandler;
use Copie\Contexts\Beneficiary\Application\Query\ListBeneficiariesQuery;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class ListBeneficiariesHandlerTest extends TestCase
{
    /**
     * @var MockObject&BeneficiaryQueryInterface
     */
    private MockObject $beneficiaryQuery;

    private ListBeneficiariesHandler $listBeneficiariesHandler;

    protected function setUp(): void
    {
        parent::setUp();
        $this->beneficiaryQuery = $this->createMock(BeneficiaryQueryInterface::class);
        $this->listBeneficiariesHandler = new ListBeneficiariesHandler($this->beneficiaryQuery);
    }

    #[Test]
    public function test_handle_returns_paginator(): void
    {
        $paginator = $this->createStub(LengthAwarePaginator::class);

        $this->beneficiaryQuery
            ->expects($this->once())
            ->method('findAllPaginated')
            ->willReturn($paginator);

        $listBeneficiariesQuery = new ListBeneficiariesQuery([], 15);
        $result = $this->listBeneficiariesHandler->handle($listBeneficiariesQuery);

        $this->assertSame($paginator, $result);
    }

    #[Test]
    public function test_handle_passes_default_filters_and_per_page(): void
    {
        $capturedArgs = null;

        $this->beneficiaryQuery
            ->expects($this->exactly(1))
            ->method('findAllPaginated')
            ->willReturnCallback(function (array $filters, int $perPage) use (&$capturedArgs): MockObject {
                $capturedArgs = [$filters, $perPage];

                return $this->createMock(LengthAwarePaginator::class);
            });

        $listBeneficiariesQuery = new ListBeneficiariesQuery();
        $this->listBeneficiariesHandler->handle($listBeneficiariesQuery);

        $this->assertSame([], $capturedArgs[0]);
        $this->assertSame(15, $capturedArgs[1]);
    }

    #[Test]
    public function test_handle_passes_custom_filters_and_per_page(): void
    {
        $capturedArgs = null;

        $this->beneficiaryQuery
            ->expects($this->exactly(1))
            ->method('findAllPaginated')
            ->willReturnCallback(function (array $filters, int $perPage) use (&$capturedArgs): MockObject {
                $capturedArgs = [$filters, $perPage];

                return $this->createMock(LengthAwarePaginator::class);
            });

        $listBeneficiariesQuery = new ListBeneficiariesQuery(['type' => 'child'], 10);
        $this->listBeneficiariesHandler->handle($listBeneficiariesQuery);

        $this->assertSame(['type' => 'child'], $capturedArgs[0]);
        $this->assertSame(10, $capturedArgs[1]);
    }
}
