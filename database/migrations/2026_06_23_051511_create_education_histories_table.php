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
        // Education Histories Table
        Schema::create('education_histories', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('foster_child_id')
                ->constrained('foster_child', 'id')
                ->cascadeOnDelete();
            $table->enum('school_level', ['kindergarten', 'elementary', 'middle_school', 'high_school', 'vocational_school', 'university', 'non_formal']);
            $table->string('school_name')->comment('Name of the institution (e.g. "SMK Negeri 1")');
            $table->year('admission_year');
            $table->year('graduation_year')->nullable();
            $table->enum('status', ['graduated', 'dropped_out', 'transferred', 'currently_enrolled'])->comment('E.g. "graduated", "dropped_out", "transferred", "currently_enrolled"');
            $table->text('dropout_reason')->nullable()->comment('Reason for dropping out, if applicable. Crucial for social and pychologoical assessment of the child.');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('education_histories');
    }
};
