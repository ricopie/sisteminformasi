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
            $table->enum('type', array_column(BeneficiaryType::cases(), 'value'));
            $table->text('full_name');
            $table->text('nick_name')->nullable();
            $table->text('birth_place');
            $table->text('birth_date');
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
