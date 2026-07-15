<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('beneficiaries', function (Blueprint $blueprint): void {
            // Primary key — ULID as string
            $blueprint->string('id')->primary();

            // PII (High) — encrypted by CipherSweet
            $blueprint->text('nik')->unique();
            $blueprint->text('nik_blind_index'); // Blind index for NIK search

            // Non-PII
            $blueprint->string('type');

            // PII (High) — encrypted by CipherSweet
            $blueprint->text('first_name');
            $blueprint->text('last_name');

            // Non-PII
            $blueprint->string('nick_name')->nullable();

            // PII (Medium) — encrypted by CipherSweet
            $blueprint->text('birth_place');
            $blueprint->text('birth_date');

            // Non-PII
            $blueprint->string('gender');

            // PII (High) — Family Card, encrypted by CipherSweet
            $blueprint->text('family_card_number');
            $blueprint->text('family_card_head_of_family_name');

            // PII (Medium) — Address, encrypted by CipherSweet
            $blueprint->text('family_card_address')->nullable();

            // Non-PII
            $blueprint->boolean('is_active')->default(value: true);
            $blueprint->json('specific_attributes')->nullable();

            // Guardians stored as JSON within aggregate
            $blueprint->json('guardians')->nullable();

            $blueprint->timestamps();

            // Index for active status filtering
            $blueprint->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('beneficiaries');
    }
};
