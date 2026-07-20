<?php

declare(strict_types=1);

namespace Tests\Unit\Contexts\Beneficiary\Application\Query;

use Copie\Contexts\Beneficiary\Application\Query\GetBeneficiaryQuery;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class GetBeneficiaryQueryTest extends TestCase
{
    #[Test]
    public function test_from_array_creates_query_with_id(): void
    {
        $data = ['id' => '01JARZ3NDEKTSV4RRFFXJJNW43'];

        $getBeneficiaryQuery = GetBeneficiaryQuery::fromArray($data);

        $this->assertSame($data['id'], $getBeneficiaryQuery->id);
    }

    #[Test]
    public function test_to_array_returns_id(): void
    {
        $getBeneficiaryQuery = new GetBeneficiaryQuery(id: 'test-id');

        $this->assertSame(['id' => 'test-id'], $getBeneficiaryQuery->toArray());
    }

    #[Test]
    public function test_round_trip(): void
    {
        $original = ['id' => 'xyz'];
        $getBeneficiaryQuery = GetBeneficiaryQuery::fromArray($original);
        $result = $getBeneficiaryQuery->toArray();

        $this->assertSame($original, $result);
    }
}
