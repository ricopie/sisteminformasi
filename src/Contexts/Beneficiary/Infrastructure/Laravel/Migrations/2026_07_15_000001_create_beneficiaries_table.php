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
            $table->string('id')->primary();
            $table->text('nik')->unique();
            $table->text('nik_blind_index');
            $table->string('type');
            $table->text('first_name');
            $table->text('last_name');
            $table->string('nick_name')->nullable();
            $table->text('birth_place');
            $table->text('birth_date');
            $table->string('gender');
            $table->text('family_card_number');
            $table->text('family_card_head_of_family_name');
            $table->text('family_card_address')->nullable();
            $table->boolean('is_active')->default(value: true);
            $table->index('is_active'); // Index for active status filtering
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
