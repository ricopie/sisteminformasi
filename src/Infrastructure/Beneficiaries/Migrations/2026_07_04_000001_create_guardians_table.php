<?php

use Domain\Beneficiaries\ValueObjects\Enum\GuardianRelationship;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('guardians', function (Blueprint $table): void {
            $table->ulid('id')->primary();
            $table->foreignUlid('beneficiary_id')->constrained('beneficiary', 'id')->cascadeOnDelete();
            // Person from VO have name, occupation, education level, address, contact
            $table->text('person');
            $table->enum('relationship', array_column(GuardianRelationship::cases(), 'value'));
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('guardians');
    }
};
