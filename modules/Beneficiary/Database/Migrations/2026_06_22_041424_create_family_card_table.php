<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('family_card', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->text('family_card_number');
            $table->string('family_card_number_hash', 255)->unique()->nullable();
            $table->string('head_of_family_name', 150);
            $table->json('address')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('family_card');
    }
};
