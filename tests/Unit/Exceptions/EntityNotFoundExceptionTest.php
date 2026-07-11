<?php

namespace Tests\Unit\Exceptions;

use PHPUnit\Framework\Attributes\Test;
use RuntimeException;
use Shared\Exceptions\EntityNotFoundException;
use Tests\TestCase;

class EntityNotFoundExceptionTest extends TestCase
{
    #[Test]
    public function it_can_create_exception_for_id(): void
    {
        $exception = EntityNotFoundException::forId('123', 'User');
        $this->assertInstanceOf(EntityNotFoundException::class, $exception);
        $this->assertInstanceOf(RuntimeException::class, $exception);
        $this->assertStringContainsString("User with ID '123' not found.", $exception->getMessage());
    }

    #[Test]
    public function it_contains_correct_id_and_entity_type_in_message(): void
    {
        $exception = EntityNotFoundException::forId('abc-456', 'Product');
        $this->assertStringContainsString("Product with ID 'abc-456' not found.", $exception->getMessage());
    }
}
