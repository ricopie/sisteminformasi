<?php

declare(strict_types=1);

namespace Tests\Unit\Contexts\Beneficiary\Application\Query;

use Copie\Contexts\Beneficiary\Application\Query\ListBeneficiariesQuery;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class ListBeneficiariesQueryTest extends TestCase
{
    #[Test]
    public function test_from_array_creates_query_with_defaults(): void
    {
        $data = [];

        $listBeneficiariesQuery = ListBeneficiariesQuery::fromArray($data);

        $this->assertSame([], $listBeneficiariesQuery->filters);
        $this->assertSame(15, $listBeneficiariesQuery->perPage);
    }

    #[Test]
    public function test_from_array_creates_query_with_custom_values(): void
    {
        $data = [
            'filters' => ['type' => 'child'],
            'perPage' => 25,
        ];

        $listBeneficiariesQuery = ListBeneficiariesQuery::fromArray($data);

        $this->assertSame(['type' => 'child'], $listBeneficiariesQuery->filters);
        $this->assertSame(25, $listBeneficiariesQuery->perPage);
    }

    #[Test]
    public function test_to_array_returns_correct_structure(): void
    {
        $listBeneficiariesQuery = new ListBeneficiariesQuery(
            filters: ['type' => 'child'],
            perPage: 25,
        );

        $expected = [
            'filters' => ['type' => 'child'],
            'perPage' => 25,
        ];

        $this->assertSame($expected, $listBeneficiariesQuery->toArray());
    }

    #[Test]
    public function test_round_trip_from_array_to_array(): void
    {
        $original = [
            'filters' => ['type' => 'child'],
            'perPage' => 25,
        ];

        $listBeneficiariesQuery = ListBeneficiariesQuery::fromArray($original);
        $result = $listBeneficiariesQuery->toArray();

        $this->assertSame($original, $result);
    }
}
