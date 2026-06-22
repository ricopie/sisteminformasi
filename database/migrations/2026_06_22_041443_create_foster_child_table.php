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
        Schema::create('foster_child', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->string('fullname');
            $table->string('nickname', 10)->nullable();
            $table->text('nik_encrypted');
            $table->string('nik_hash', 255)->unique();
            $table->foreignUlid('family_card_id')
                ->constrained('family_card', 'id')
                ->cascadeOnDelete();
            $table->string('birth_place', 50);
            $table->date('birth_date');
            $table->char('gender', 1)->comment('M = Male, F = Female');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('foster_child');
    }
};
