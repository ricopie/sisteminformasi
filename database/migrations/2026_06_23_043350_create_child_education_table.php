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
        // Child Education Table
        Schema::create('child_educations', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('foster_child_id')
                ->constrained('foster_child', 'id')
                ->cascadeOnDelete();
            $table->enum('education_status', ['enrolled', 'dropped_out', 'not_enrolled', 'graduated']);
            $table->enum('school_level', ['kindergarten', 'elementary', 'middle_school', 'high_school', 'vocational_school', 'university', 'non_formal']);
            $table->string('school_name', 20)->comment('Name of the institution (e.g. "SMK Negeri 1")');
            $table->string('current_grade', 12)->comment('E.g. "Grade 5", "Grade 11", or "Semester 3"');
            $table->string('major')->nullable()->comment('E.g. "Science", "Social Studies", or "Computer Science"');
            $table->string('student_id_number')->nullable()->comment('Student identification number provided by the school like NISN or NIM');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('child_educations');
    }
};
