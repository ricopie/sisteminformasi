<?php

namespace Tests\Unit\ValueObjects;

use PHPUnit\Framework\Attributes\Test;
use Shared\Exceptions\InvalidIdentifierException;
use Shared\ValueObjects\DomainId;
use Tests\TestCase;

class DomainIdTest extends TestCase
{
    #[Test]
    public function it_can_create_domain_id_with_valid_string(): void
    {
        $domainId = new DomainId('test-id-123');
        $this->assertInstanceOf(DomainId::class, $domainId);
        $this->assertEquals('test-id-123', $domainId->value);
    }

    #[Test]
    public function it_throws_invalid_identifier_exception_when_value_is_empty(): void
    {
        $this->expectException(InvalidIdentifierException::class);
        new DomainId('');
    }

    #[Test]
    public function it_throws_invalid_identifier_exception_when_value_is_whitespace_only(): void
    {
        $this->expectException(InvalidIdentifierException::class);
        new DomainId('   ');
    }

    #[Test]
    public function it_can_compare_equal_ids(): void
    {
        $id1 = new DomainId('same-id');
        $id2 = new DomainId('same-id');
        $this->assertTrue($id1->equals($id2));
    }

    #[Test]
    public function it_can_compare_different_ids(): void
    {
        $id1 = new DomainId('id-1');
        $id2 = new DomainId('id-2');
        $this->assertFalse($id1->equals($id2));
    }

    #[Test]
    public function it_can_convert_to_string(): void
    {
        $domainId = new DomainId('string-representation');
        $this->assertEquals('string-representation', (string) $domainId);
    }
}
