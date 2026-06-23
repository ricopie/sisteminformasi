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
        // Education Funding Table
        Schema::create('education_fundings', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('foster_child_id')
                ->constrained('foster_child', 'id')
                ->cascadeOnDelete();
            $table->enum('funding_source', ['government_scholarship', 'private_scholarship', 'family_support', 'self_funded', 'other'])->comment('Source of education funding');
            $table->decimal('amount', 15, 2)->comment('Amount of funding provided for education expenses');
            $table->string('currency', 3)->comment('Currency code (e.g., "USD", "IDR")');
            $table->date('funding_start_date')->comment('Date when the funding started');
            $table->date('funding_end_date')->nullable()->comment('Date when the funding ended, if applicable');
            $table->decimal('monthly_tuition_fee', 15, 2)->nullable()->comment('Monthly tuition fee covered by the funding');
            $table->text('urgent_needs')->nullable()->comment('Description of any urgent needs related to education funding (e.g., "Needs laptop for online learning")');
            $table->enum('funding_status', ['active', 'inactive', 'pending'])->comment('Current status of the funding');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('education_fundings');
    }
};
