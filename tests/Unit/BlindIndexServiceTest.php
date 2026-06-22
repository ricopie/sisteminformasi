<?php

namespace Tests\Unit;

use App\Services\BlindIndexService;
use Tests\TestCase;

class BlindIndexServiceTest extends TestCase
{
    private BlindIndexService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = $this->app->make(BlindIndexService::class);
    }

    public function test_encrypt_returns_encrypted_and_hash(): void
    {
        $result = $this->service->encrypt('1234567890123456');

        $this->assertArrayHasKey('encrypted', $result);
        $this->assertArrayHasKey('hash', $result);
        $this->assertIsString($result['encrypted']);
        $this->assertIsString($result['hash']);
    }

    public function test_decrypt_returns_original_value(): void
    {
        $plain = '1234567890123456';
        $encrypted = $this->service->encrypt($plain);

        $this->assertSame($plain, $this->service->decrypt($encrypted['encrypted']));
    }

    public function test_hash_returns_base64_string(): void
    {
        $hash = $this->service->hash('1234567890123456');

        $this->assertMatchesRegularExpression('/^[A-Za-z0-9+\/=]+$/', $hash);
        $this->assertSame(44, strlen($hash));
    }

    public function test_hash_is_deterministic(): void
    {
        $input = '1234567890123456';

        $hash1 = $this->service->hash($input);
        $hash2 = $this->service->hash($input);

        $this->assertSame($hash1, $hash2);
    }

    public function test_hash_differs_for_different_inputs(): void
    {
        $hash1 = $this->service->hash('1234567890123456');
        $hash2 = $this->service->hash('1234567890123457');

        $this->assertNotSame($hash1, $hash2);
    }

    public function test_normalize_strips_non_digits(): void
    {
        $this->assertSame('1234567890123456', $this->service->normalize('1234-5678-9012-3456'));
        $this->assertSame('1234567890123456', $this->service->normalize('1234 5678 9012 3456'));
        $this->assertSame('1234567890123456', $this->service->normalize('1234.5678.9012.3456'));
        $this->assertSame('1234567890123456', $this->service->normalize(' 1234567890123456 '));
    }

    public function test_hash_is_same_after_normalize(): void
    {
        $this->assertSame(
            $this->service->hash('1234567890123456'),
            $this->service->hash('1234-5678-9012-3456')
        );
    }

    public function test_decrypt_returns_normalized_value(): void
    {
        $result = $this->service->encrypt('1234-5678-9012-3456');

        $this->assertSame('1234567890123456', $this->service->decrypt($result['encrypted']));
    }
}
