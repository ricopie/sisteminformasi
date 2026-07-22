<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('beneficiaries', function (Blueprint $table): void {
            $table->string('id', 26)->primary();
            $table->string('nik', 16);
            $table->string('nik_blind_index', 64)->index();
            $table->string('type', 20);
            $table->string('first_name');
            $table->string('last_name');
            $table->string('nick_name', 50)->nullable();
            $table->string('birth_place');
            $table->string('birth_date', 20);
            $table->string('gender', 10);
            $table->string('family_card_number', 30);
            $table->string('family_card_head_of_family_name');
            $table->json('family_card_address')->nullable();
            $table->boolean('is_active')->default(false);
            $table->json('specific_attributes')->nullable();
            $table->json('guardians')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('beneficiaries');
    }
};
