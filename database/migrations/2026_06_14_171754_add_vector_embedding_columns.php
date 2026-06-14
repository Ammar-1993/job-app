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
        Schema::table('job_vacancies', function (Blueprint $table) {
            $table->longText('vector_embedding')->nullable();
        });

        Schema::table('resumes', function (Blueprint $table) {
            $table->longText('vector_embedding')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('job_vacancies', function (Blueprint $table) {
            $table->dropColumn('vector_embedding');
        });

        Schema::table('resumes', function (Blueprint $table) {
            $table->dropColumn('vector_embedding');
        });
    }
};
