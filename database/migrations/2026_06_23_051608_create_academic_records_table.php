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
        // Academic Records Table
        Schema::create('academic_records', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('foster_child_id')
                ->constrained('foster_child', 'id')
                ->cascadeOnDelete();
            $table->string('academic_year')->comment('Academic year in the format "YYYY/YYYY" (e.g., "2023/2024")');
            $table->enum('semester', ['odd', 'even'])->comment('Semester of the academic year, either "odd" or "even"');
            $table->decimal('gpa', 3, 2)->comment('Grade Point Average for the semester, e.g., 3.75');
            $table->integer('class_rank')->comment('Rank of the student in their class');
            $table->text('achivements')->nullable();
            $table->text('caregiver_notes')->nullable()->comment('Evaluation (e.g. "Needs extra tutoring in Mathematics)');
            $table->string('report_card_path')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('academic_records');
    }
};
