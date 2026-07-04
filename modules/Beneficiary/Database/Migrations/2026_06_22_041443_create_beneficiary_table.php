<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Modules\Beneficiary\Enums\BeneficiaryType;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('beneficiary', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->text('nik');
            $table->string('nik_index', 255)->unique()->nullable();
            $table->enum('type', array_column(BeneficiaryType::cases(), 'value'));
            $table->string('full_name');
            $table->string('nick_name', 10)->nullable();
            $table->string('birth_place', 50);
            $table->date('birth_date');
            $table->char('gender', 1)->comment('M = Male, F = Female');
            $table->text('attributes')->nullable();
            $table->foreignUlid('family_card_id')
                ->constrained('family_card', 'id')
                ->cascadeOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('beneficiary');
    }
};
