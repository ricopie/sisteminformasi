<?php

declare(strict_types=1);

namespace Tests\Unit\Shared\Domain\ValueObjects;

use Copie\Shared\Domain\Exceptions\InvalidIdentifierException;
use Copie\Shared\Domain\ValueObjects\DomainId;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class DomainIdTest extends TestCase
{
    #[Test]
    public function valid_ulid_is_accepted(): void
    {
        // $ulid = '01KXWFAPNVT7WP6YN37ZWTDDR8';
        $ulid = (string) DomainId::generate();
        $domainId = new DomainId($ulid);
        $this->assertSame($ulid, $domainId->value);
    }

    #[Test]
    public function generate_produces_valid_ulid(): void
    {
        $domainId = DomainId::generate();
        $this->assertTrue((new DomainId($domainId->value))->equals($domainId));
    }

    #[Test]
    public function from_string_creates_domain_id_from_valid_string(): void
    {
        // $ulid = '01KXWFAPNVT7WP6YN37ZWTDDR8';
        $ulid = (string) DomainId::generate();
        $domainId = DomainId::fromString($ulid);
        $this->assertSame($ulid, $domainId->value);
    }

    #[Test]
    public function invalid_ulid_throws_invalid_identifier_exception(): void
    {
        $this->expectException(InvalidIdentifierException::class);
        new DomainId('invalid-ulid');
    }

    #[Test]
    public function equals_returns_true_for_same_id(): void
    {
        // $ulid = '01KXWFAPNVT7WP6YN37ZWTDDR8';
        $ulid = (string) DomainId::generate();
        $domainId1 = new DomainId($ulid);
        $domainId2 = new DomainId($ulid);
        $this->assertTrue($domainId1->equals($domainId2));
    }

    #[Test]
    public function equals_returns_false_for_different_ids(): void
    {
        $domainId1 = new DomainId('01KXWFAPNVT7WP6YN37ZWTDDR8');
        $domainId2 = new DomainId('01KXWFBG9GD2Y120T1TXESCZ4Z');
        $this->assertFalse($domainId1->equals($domainId2));
    }

    #[Test]
    public function __to_string_returns_the_ulid_string(): void
    {
        // $ulid = '01KXWFAPNVT7WP6YN37ZWTDDR8';
        $ulid = (string) DomainId::generate();
        $domainId = new DomainId($ulid);
        $this->assertSame($ulid, (string) $domainId);
    }

    #[Test]
    public function value_property_holds_the_ulid_string(): void
    {
        // $ulid = '01KXWFAPNVT7WP6YN37ZWTDDR8';
        $ulid = (string) DomainId::generate();
        $domainId = new DomainId($ulid);
        $this->assertSame($ulid, $domainId->value);
    }
}
