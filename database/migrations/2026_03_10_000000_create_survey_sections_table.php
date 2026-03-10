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
        Schema::create('survey_sections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('survey_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->text('description')->nullable();
            $table->integer('order')->default(0);
            $table->timestamps();
        });

        // Add section_id to survey_questions
        Schema::table('survey_questions', function (Blueprint $table) {
            $table->foreignId('section_id')->nullable()->after('survey_id')->constrained('survey_sections')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('survey_questions', function (Blueprint $table) {
            $table->dropForeignKey(['section_id']);
            $table->dropColumn('section_id');
        });

        Schema::dropIfExists('survey_sections');
    }
};
