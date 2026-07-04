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
            $table->string('family_card_number_index', 255)->unique()->nullable();
            $table->text('head_of_family_name');
            $table->string('head_of_family_name_index', 255)->unique()->nullable();
            $table->text('address')->nullable();
            $table->string('address_index', 255)->unique()->nullable();
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
