<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('blind_indexes', function (Blueprint $table) {
            $table->string('indexable_id', 26)->change();
        });
    }

    public function down(): void
    {
        Schema::table('blind_indexes', function (Blueprint $table) {
            $table->bigInteger('indexable_id')->change();
        });
    }
};
