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
            $table->text('family_card_number_encrypted');
            $table->string('family_card_number_hash', 255)->unique();
            $table->string('head_of_family_name', 150);

            $table->string('address', 150);
            $table->string('rt', 3);
            $table->string('rw', 3);
            $table->string('village', 150);
            $table->string('sub_district', 150);
            $table->string('city', 150);
            $table->string('province', 150);
            $table->string('postal_code', 5);
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
